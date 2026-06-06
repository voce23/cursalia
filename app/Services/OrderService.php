<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\NewEnrollmentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Crea la orden y matricula los cursos del carrito.
     *
     * @param  float|null  $confirmedAmount  Monto realmente confirmado/cobrado por la pasarela.
     *                                        Si se provee, DEBE coincidir con el total del carrito
     *                                        o la operación se aborta (no se matricula).
     */
    public static function storeOrder(
        string $transactionId,
        string $paymentMethod,
        int $userId,
        ?float $confirmedAmount = null,
        ?string $currency = null
    ): Order {
        // Idempotencia: si ya existe una orden con este transaction_id, devolverla
        // (debe ir ANTES de cualquier cálculo para que el webhook posterior al
        // redirect síncrono no re-procese ni falle la verificación de monto).
        $existing = Order::where('transaction_id', $transactionId)->first();
        if ($existing) {
            return $existing;
        }

        $cartItems = Cart::where('user_id', $userId)
            ->with('course')
            ->get();

        $total = $cartItems->sum(fn ($item) => $item->course->discount > 0
            ? $item->course->discount
            : $item->course->price);

        // Verificación de monto (C2/C3): el importe confirmado por la pasarela debe
        // coincidir con el total real del carrito calculado en el servidor. Bloquea
        // manipulación del carrito tras pagar y montos forjados desde el cliente.
        if ($confirmedAmount !== null && abs((float) $confirmedAmount - (float) $total) > 0.01) {
            Log::warning('payment.amount_mismatch', [
                'transaction_id' => $transactionId,
                'user_id'        => $userId,
                'payment_method' => $paymentMethod,
                'confirmed'      => $confirmedAmount,
                'cart_total'     => $total,
            ]);

            throw new \RuntimeException('El monto pagado no coincide con el total del carrito.');
        }

        try {
            return DB::transaction(function () use ($cartItems, $total, $transactionId, $paymentMethod, $userId, $confirmedAmount, $currency) {
                $order = Order::create([
                    'invoice_id'     => 'INV-' . strtoupper(Str::random(10)),
                    'buyer_id'       => $userId,
                    'status'         => 'completed',
                    'total_amount'   => $total,
                    'paid_amount'    => $confirmedAmount ?? $total,
                    'currency'       => $currency ?: config('paypal.currency', 'USD'),
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $transactionId,
                ]);

                $commissionRate = (float) config('commission.rate', 20);

                foreach ($cartItems as $item) {
                    $price = $item->course->discount > 0
                        ? $item->course->discount
                        : $item->course->price;

                    $platformEarning   = round($price * ($commissionRate / 100), 2);
                    $instructorEarning = round($price - $platformEarning, 2);

                    OrderItem::create([
                        'order_id'           => $order->id,
                        'course_id'          => $item->course_id,
                        'price'              => $price,
                        'commission_rate'    => $commissionRate,
                        'platform_earning'   => $platformEarning,
                        'instructor_earning' => $instructorEarning,
                    ]);

                    Enrollment::firstOrCreate(
                        ['user_id' => $userId, 'course_id' => $item->course_id],
                        ['instructor_id' => $item->course->instructor_id, 'have_access' => true]
                    );

                    // Notificar al instructor sobre nueva matrícula
                    $instructor = User::find($item->course->instructor_id);
                    $student    = User::find($userId);
                    if ($instructor && $student) {
                        $instructor->notify(new NewEnrollmentNotification($item->course, $student));
                    }
                }

                Cart::where('user_id', $userId)->delete();

                return $order;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Posible violación del índice único de transaction_id por carrera
            // (webhook + redirect llegando casi a la vez) → devolver la orden existente.
            $existing = Order::where('transaction_id', $transactionId)->first();
            if ($existing) {
                return $existing;
            }

            throw $e;
        }
    }
}
