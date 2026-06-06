<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api as RazorpayApi;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PaymentController extends Controller
{
    // ─── Helpers comunes ────────────────────────────────

    private function cartTotal()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('course')
            ->get();

        if ($cartItems->isEmpty()) {
            return [null, 0];
        }

        $total = $cartItems->sum(fn ($item) => $item->course->discount > 0
            ? $item->course->discount
            : $item->course->price);

        return [$cartItems, $total];
    }
    /**
     * Obtiene la URL base y las credenciales según el modo (sandbox/live).
     */
    private function paypalConfig(): array
    {
        $mode = config('paypal.mode', 'sandbox');

        return [
            'base_url'      => $mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com',
            'client_id'     => config("paypal.{$mode}.client_id"),
            'client_secret' => config("paypal.{$mode}.client_secret"),
        ];
    }

    /**
     * Obtiene un access token de PayPal vía OAuth 2.0.
     */
    private function getAccessToken(): ?string
    {
        $cfg = $this->paypalConfig();

        $response = Http::asForm()
            ->withBasicAuth($cfg['client_id'], $cfg['client_secret'])
            ->post("{$cfg['base_url']}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json('access_token');
    }

    /**
     * Crea una orden en PayPal y redirige al usuario para aprobarla.
     */
    public function payWithPaypal()
    {
        [$cartItems, $total] = $this->cartTotal();

        if (! $cartItems) {
            return redirect()->route('cart.index');
        }

        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return redirect()->route('order.failed');
        }

        $cfg = $this->paypalConfig();

        $response = Http::withToken($accessToken)
            ->post("{$cfg['base_url']}/v2/checkout/orders", [
                'intent'          => 'CAPTURE',
                'purchase_units'  => [[
                    'amount' => [
                        'currency_code' => config('paypal.currency', 'USD'),
                        'value'         => number_format($total, 2, '.', ''),
                    ],
                    'custom_id' => (string) Auth::id(),
                ]],
                'application_context' => [
                    'return_url' => route('paypal.success'),
                    'cancel_url' => route('paypal.cancel'),
                ],
            ]);

        $order = $response->json();

        if (isset($order['id'])) {
            $approvalUrl = collect($order['links'])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            if ($approvalUrl) {
                return redirect()->away($approvalUrl);
            }
        }

        return redirect()->route('order.failed');
    }

    /**
     * PayPal redirige aquí tras la aprobación. Capturamos el pago.
     */
    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect()->route('order.failed');
        }

        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return redirect()->route('order.failed');
        }

        $cfg = $this->paypalConfig();

        $response = Http::withToken($accessToken)
            ->post("{$cfg['base_url']}/v2/checkout/orders/{$token}/capture");

        $result = $response->json();

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            $pu      = $result['purchase_units'][0] ?? [];
            $capture = $pu['payments']['captures'][0] ?? [];

            // Verificar que la orden de PayPal pertenece al usuario autenticado
            $customId = $pu['custom_id'] ?? ($capture['custom_id'] ?? null);
            if ((string) $customId !== (string) Auth::id()) {
                return redirect()->route('order.failed');
            }

            $transactionId = $capture['id'] ?? '';
            $captured = isset($capture['amount']['value']) ? (float) $capture['amount']['value'] : null;
            $currency = $capture['amount']['currency_code'] ?? null;

            try {
                OrderService::storeOrder($transactionId, 'paypal', Auth::id(), $captured, $currency);
            } catch (\RuntimeException $e) {
                return redirect()->route('order.failed');
            }

            return redirect()->route('order.success');
        }

        return redirect()->route('order.failed');
    }

    /**
     * El usuario canceló el pago en PayPal.
     */
    public function paypalCancel()
    {
        flash()->warning('Pago cancelado. Tu carrito sigue intacto.');
        return redirect()->route('cart.index');
    }

    // ─── STRIPE ─────────────────────────────────────────

    /**
     * Crea una Checkout Session de Stripe y redirige.
     */
    public function payWithStripe()
    {
        [$cartItems, $total] = $this->cartTotal();

        if (! $cartItems) {
            return redirect()->route('cart.index');
        }

        try {
            Stripe::setApiKey(config('stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => config('stripe.currency', 'USD'),
                        'product_data' => [
                            'name' => 'Compra de cursos LMSL13',
                        ],
                        'unit_amount' => (int) round($total * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'        => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('stripe.cancel'),
                'metadata'    => ['user_id' => Auth::id()],
            ]);

            return redirect()->away($session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);
            flash()->error('No se pudo iniciar el pago con Stripe. Intenta nuevamente.');
            return redirect()->route('cart.index');
        }
    }

    /**
     * Stripe redirige aquí tras el pago exitoso.
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('order.failed');
        }

        try {
            Stripe::setApiKey(config('stripe.secret'));

            $session = StripeSession::retrieve($sessionId);

            // Verificar que la sesión de Stripe pertenece al usuario autenticado
            if ((string) ($session->metadata->user_id ?? '') !== (string) Auth::id()) {
                return redirect()->route('order.failed');
            }

            if ($session->payment_status === 'paid') {
                $transactionId = $session->payment_intent;
                $confirmed = isset($session->amount_total) ? (float) $session->amount_total / 100 : null;
                $currency  = isset($session->currency) ? strtoupper($session->currency) : null;

                try {
                    OrderService::storeOrder($transactionId, 'stripe', Auth::id(), $confirmed, $currency);
                } catch (\RuntimeException $e) {
                    return redirect()->route('order.failed');
                }

                return redirect()->route('order.success');
            }

            return redirect()->route('order.failed');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);
            return redirect()->route('order.failed');
        }
    }

    /**
     * El usuario canceló el pago en Stripe.
     */
    public function stripeCancel()
    {
        flash()->warning('Pago cancelado. Tu carrito sigue intacto.');
        return redirect()->route('cart.index');
    }

    // ─── RAZORPAY ───────────────────────────────────────

    /**
     * Muestra la página intermedia que abre el widget de Razorpay.
     */
    public function razorpayRedirect()
    {
        [$cartItems, $total] = $this->cartTotal();

        if (! $cartItems) {
            return redirect()->route('cart.index');
        }

        // Razorpay trabaja en la subunidad más pequeña (paise para INR, cents para USD)
        $amountInSmallestUnit = (int) round($total * 100);

        return view('frontend.razorpay-redirect', [
            'razorpayKey' => config('razorpay.key'),
            'amount'      => $amountInSmallestUnit,
            'currency'    => config('razorpay.currency', 'INR'),
            'userName'    => Auth::user()->name,
            'userEmail'   => Auth::user()->email,
        ]);
    }

    /**
     * Razorpay envía el payment_id vía POST tras el pago.
     */
    public function payWithRazorpay(Request $request)
    {
        $paymentId = $request->input('razorpay_payment_id');

        if (! $paymentId) {
            return redirect()->route('order.failed');
        }

        $api = new RazorpayApi(
            config('razorpay.key'),
            config('razorpay.secret')
        );

        try {
            [$cartItems, $total] = $this->cartTotal();
            if (! $cartItems) {
                return redirect()->route('cart.index');
            }
            $amountInSmallestUnit = (int) round($total * 100);

            $payment = $api->payment->fetch($paymentId);

            // El monto autorizado por Razorpay debe coincidir con el total del carrito
            if ((int) $payment->amount !== $amountInSmallestUnit) {
                report(new \RuntimeException('Razorpay: el monto no coincide con el carrito.'));
                return redirect()->route('order.failed');
            }

            if ($payment->status === 'authorized') {
                $payment->capture(['amount' => $amountInSmallestUnit]);
                $payment = $api->payment->fetch($paymentId);
            }

            if ($payment->status === 'captured') {
                $confirmed = (float) $payment->amount / 100;
                try {
                    OrderService::storeOrder($paymentId, 'razorpay', Auth::id(), $confirmed, $payment->currency ?? null);
                } catch (\RuntimeException $e) {
                    return redirect()->route('order.failed');
                }
                return redirect()->route('order.success');
            }

            return redirect()->route('order.failed');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('order.failed');
        }
    }

    // ─── Vistas de resultado ────────────────────────────

    public function orderSuccess()
    {
        return view('frontend.order-success');
    }

    public function orderFailed()
    {
        return view('frontend.order-failed');
    }
}
