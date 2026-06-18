<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOrder;
use App\Models\Enrollment;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

/**
 * Compra de UN curso con Stripe (tarjeta) o PayPal.
 *
 * Complemento "Pagos internacionales". A diferencia del motor original
 * (carrito + config estática), este flujo:
 *   - Lee las claves del panel Admin → Pagos (modelo PaymentSetting, cifrado).
 *   - Compra directa de un solo curso (sin carrito) → simple para el dueño.
 *   - Al confirmarse el pago, inscribe al alumno (Enrollment) y lo lleva al curso.
 *
 * Requiere que el complemento esté activado con la llave PAY (PaymentSettingController::isActive()).
 */
class CourseCheckoutController extends Controller
{
    /** Claves del panel Admin → Pagos, ya descifradas. */
    private function settings(): array
    {
        return PaymentSetting::pluck('value', 'key')
            ->map(fn ($v, $k) => PaymentSetting::decryptIfSensitive($k, $v))
            ->all();
    }

    /** Precio efectivo del curso (con descuento si lo hay). */
    private function price(Course $course): float
    {
        return (float) ($course->discount > 0 ? $course->discount : $course->price);
    }

    /** Curso comprable: aprobado, activo, de pago y con el complemento activo. */
    private function ensureBuyable(Course $course): void
    {
        abort_unless(PaymentSettingController::isActive(), 403, 'Los pagos no están activados.');
        abort_unless(
            $course->is_approved === 'approved'
                && $course->status === 'active'
                && $this->price($course) > 0,
            404,
            'Este curso no está a la venta.'
        );
    }

    /**
     * Inscribe al alumno autenticado (idempotente), registra la venta como aprobada
     * (Stripe/PayPal son automáticos) y lo lleva al reproductor.
     */
    private function enroll(Course $course, string $method, ?string $txn = null, string $currency = 'USD', ?float $amount = null)
    {
        // withTrashed: si la inscripción fue retirada antes (borrado suave), se
        // recupera en vez de chocar con el índice único al recomprar.
        $enrollment = Enrollment::withTrashed()->firstOrNew([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
        ]);
        $enrollment->instructor_id = $course->instructor_id;
        $enrollment->have_access = true;
        if ($enrollment->trashed()) {
            $enrollment->restore();
        }
        $enrollment->save();

        // Idempotente: si el alumno recarga la página de éxito, la transacción ya
        // registrada NO crea una venta duplicada (se busca por transaction_id).
        $attributes = $txn
            ? ['method' => $method, 'transaction_id' => $txn]
            : ['user_id' => Auth::id(), 'course_id' => $course->id, 'method' => $method];

        CourseOrder::firstOrCreate($attributes, [
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'instructor_id' => $course->instructor_id,
            'method' => $method,
            'amount' => $amount ?? $this->price($course),
            'currency' => $currency,
            'status' => 'approved',
            'transaction_id' => $txn,
        ]);

        return redirect()
            ->route('student.player.show', $course)
            ->with('status', '¡Pago confirmado! Ya tienes acceso a "'.$course->title.'".');
    }

    // ─────────────────────────────── STRIPE ───────────────────────────────

    public function stripe(Course $course)
    {
        $this->ensureBuyable($course);
        abort_unless(PaymentSettingController::methodEnabled('stripe'), 404, 'El pago con tarjeta no está disponible.');
        $s = $this->settings();

        if (empty($s['stripe_secret'])) {
            return back()->with('error', 'El pago con tarjeta no está configurado todavía.');
        }

        try {
            Stripe::setApiKey($s['stripe_secret']);

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($s['stripe_currency'] ?? 'usd'),
                        'product_data' => ['name' => $course->title],
                        'unit_amount' => (int) round($this->price($course) * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => Auth::user()->email,
                'success_url' => route('checkout.stripe.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('courses.show', $course->slug),
                'metadata' => ['course_id' => $course->id, 'user_id' => Auth::id()],
            ]);

            return redirect()->away($session->url);
        } catch (ApiErrorException $e) {
            report($e);

            return back()->with('error', 'No se pudo iniciar el pago con tarjeta. Revisa tus claves de Stripe.');
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        abort_unless($sessionId, 404);

        $s = $this->settings();

        try {
            Stripe::setApiKey($s['stripe_secret'] ?? '');
            $session = StripeSession::retrieve($sessionId);

            // El pago debe pertenecer al usuario autenticado.
            if ((string) ($session->metadata->user_id ?? '') !== (string) Auth::id()) {
                return redirect('/')->with('error', 'No pudimos verificar tu pago.');
            }

            if ($session->payment_status === 'paid') {
                $course = Course::findOrFail($session->metadata->course_id);
                // Monto/moneda REALMENTE cobrados por Stripe (no recalculados).
                $amount = isset($session->amount_total) ? (float) $session->amount_total / 100 : null;

                return $this->enroll($course, 'stripe', (string) $session->payment_intent, strtoupper($session->currency ?? 'USD'), $amount);
            }
        } catch (ApiErrorException $e) {
            report($e);
        }

        return redirect('/')->with('error', 'El pago no se completó. No se hizo ningún cobro.');
    }

    // ─────────────────────────────── PAYPAL ───────────────────────────────

    private function paypal(array $s): array
    {
        $mode = ($s['paypal_mode'] ?? 'sandbox') === 'live' ? 'live' : 'sandbox';

        return [
            'base' => $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com',
            'id' => $s['paypal_client_id'] ?? '',
            'secret' => $s['paypal_client_secret'] ?? '',
            'currency' => $s['paypal_currency'] ?? 'USD',
        ];
    }

    private function paypalToken(array $cfg): ?string
    {
        return Http::asForm()
            ->withBasicAuth($cfg['id'], $cfg['secret'])
            ->post("{$cfg['base']}/v1/oauth2/token", ['grant_type' => 'client_credentials'])
            ->json('access_token');
    }

    public function paypal_start(Course $course)
    {
        $this->ensureBuyable($course);
        abort_unless(PaymentSettingController::methodEnabled('paypal'), 404, 'El pago con PayPal no está disponible.');
        $cfg = $this->paypal($this->settings());

        if (! $cfg['id'] || ! $cfg['secret']) {
            return back()->with('error', 'PayPal no está configurado todavía.');
        }

        $token = $this->paypalToken($cfg);
        if (! $token) {
            return back()->with('error', 'No se pudo conectar con PayPal. Revisa tus claves.');
        }

        $order = Http::withToken($token)
            ->post("{$cfg['base']}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $cfg['currency'],
                        'value' => number_format($this->price($course), 2, '.', ''),
                    ],
                    'custom_id' => $course->id.'|'.Auth::id(),
                    'description' => $course->title,
                ]],
                'application_context' => [
                    'brand_name' => config('app.name', 'Cursalia'),
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('checkout.paypal.success'),
                    'cancel_url' => route('courses.show', $course->slug),
                ],
            ])
            ->json();

        $approve = collect($order['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;
        if ($approve) {
            return redirect()->away($approve);
        }

        return back()->with('error', 'No se pudo iniciar el pago con PayPal.');
    }

    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token'); // order id de PayPal
        abort_unless($token, 404);

        $cfg = $this->paypal($this->settings());
        $access = $this->paypalToken($cfg);
        if (! $access) {
            return redirect('/')->with('error', 'No pudimos verificar tu pago.');
        }

        $capture = Http::withToken($access)
            ->post("{$cfg['base']}/v2/checkout/orders/{$token}/capture")
            ->json();

        if (($capture['status'] ?? '') === 'COMPLETED') {
            $custom = $capture['purchase_units'][0]['payments']['captures'][0]['custom_id']
                ?? ($capture['purchase_units'][0]['custom_id'] ?? '');
            [$courseId, $userId] = array_pad(explode('|', (string) $custom), 2, null);

            if ((string) $userId === (string) Auth::id() && $courseId) {
                $cap = $capture['purchase_units'][0]['payments']['captures'][0] ?? [];
                $txn = $cap['id'] ?? ($capture['id'] ?? null);
                // Monto/moneda REALMENTE cobrados por PayPal.
                $amount = isset($cap['amount']['value']) ? (float) $cap['amount']['value'] : null;
                $currency = $cap['amount']['currency_code'] ?? $cfg['currency'];

                return $this->enroll(Course::findOrFail($courseId), 'paypal', $txn, $currency, $amount);
            }
        }

        return redirect('/')->with('error', 'El pago no se completó. No se hizo ningún cobro.');
    }

    // ───────────────────────── QR / TRANSFERENCIA (manual) ─────────────────────────

    /** Muestra las instrucciones de pago (QR o datos bancarios) + subir comprobante. */
    public function manual(Course $course, string $method)
    {
        $this->ensureBuyable($course);
        abort_unless(in_array($method, ['qr', 'transfer'], true) && PaymentSettingController::methodEnabled($method), 404);

        return view('frontend.checkout.manual', [
            'course' => $course,
            'method' => $method,
            'price' => $this->price($course),
            's' => $this->settings(),
        ]);
    }

    /** El alumno sube su comprobante → crea un pedido PENDIENTE para que el dueño lo apruebe. */
    public function manualSubmit(Request $request, Course $course, string $method)
    {
        $this->ensureBuyable($course);
        abort_unless(in_array($method, ['qr', 'transfer'], true) && PaymentSettingController::methodEnabled($method), 404);

        // Evita comprobantes/pedidos duplicados: si ya está inscrito o ya tiene un
        // pago en revisión para este curso, no se crea otro pedido.
        $yaInscrito = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->where('have_access', true)->exists();
        $yaPendiente = CourseOrder::where('user_id', Auth::id())->where('course_id', $course->id)->where('status', 'pending')->exists();
        if ($yaInscrito || $yaPendiente) {
            return redirect()->route('courses.show', $course->slug)
                ->with('status', $yaInscrito
                    ? 'Ya tienes acceso a este curso.'
                    : 'Ya tienes un pago en revisión para este curso. Te avisaremos al confirmarlo.');
        }

        $request->validate([
            'proof' => 'required|image|max:5120',
            'reference' => 'nullable|string|max:160',
        ]);

        CourseOrder::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'instructor_id' => $course->instructor_id,
            'method' => $method,
            'amount' => $this->price($course),
            'currency' => $this->settings()['manual_currency'] ?? 'USD',
            'status' => 'pending',
            'proof_path' => $request->file('proof')->store('proofs', 'public'),
            'reference' => $request->input('reference'),
        ]);

        return redirect()
            ->route('courses.show', $course->slug)
            ->with('status', '¡Recibido! Tu pago está en revisión. Te daremos acceso al curso en cuanto lo confirmemos.');
    }
}
