<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivationKey;
use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::pluck('value', 'key')
            ->map(fn ($value, $key) => PaymentSetting::decryptIfSensitive($key, $value));

        return view('admin.payment-settings.index', [
            'settings' => $settings,
            'paymentsActive' => self::isActive(),
        ]);
    }

    /**
     * ¿Está activo el complemento de pagos internacionales?
     * (llave de activación válida guardada).
     */
    public static function isActive(): bool
    {
        $key = (string) PaymentSetting::where('key', 'payments_key')->value('value');

        return $key !== '' && ActivationKey::validate($key, 'PAY');
    }

    /** Activa el complemento de pagos con una llave (la que entrega cursalia.org). */
    public function activate(Request $request)
    {
        $request->validate(['payments_key' => 'required|string|max:40']);

        $key = strtoupper(trim($request->input('payments_key')));
        if (! ActivationKey::validate($key, 'PAY')) {
            return back()->withErrors(['payments_key' => 'La llave de pagos no es válida. Consíguela en cursalia.org/tienda.']);
        }

        PaymentSetting::updateOrCreate(['key' => 'payments_key'], ['value' => $key]);
        Cache::forget('payment_settings');
        flash()->success('¡Pagos internacionales activados! Ya puedes configurar Stripe y PayPal.');

        return back();
    }

    public function updatePaypal(Request $request)
    {
        if (! self::isActive()) {
            return back()->withErrors(['payments_key' => 'Primero activa los pagos con tu llave.']);
        }

        $validated = $request->validate([
            'paypal_mode' => 'required|in:sandbox,live',
            'paypal_client_id' => 'required|string|max:500',
            'paypal_client_secret' => 'required|string|max:500',
            'paypal_currency' => 'required|string|size:3',
        ]);

        foreach ($validated as $key => $value) {
            PaymentSetting::updateOrCreate(
                ['key' => $key],
                ['value' => PaymentSetting::encryptIfSensitive($key, $value)]
            );
        }

        Cache::forget('payment_settings');

        flash()->success('Configuración de PayPal actualizada correctamente.');

        return back();
    }

    public function updateStripe(Request $request)
    {
        if (! self::isActive()) {
            return back()->withErrors(['payments_key' => 'Primero activa los pagos con tu llave.']);
        }

        $validated = $request->validate([
            'stripe_publishable_key' => 'required|string|max:500',
            'stripe_secret' => 'required|string|max:500',
            'stripe_currency' => 'required|string|size:3',
        ]);

        foreach ($validated as $key => $value) {
            PaymentSetting::updateOrCreate(
                ['key' => $key],
                ['value' => PaymentSetting::encryptIfSensitive($key, $value)]
            );
        }

        Cache::forget('payment_settings');

        flash()->success('Configuración de Stripe actualizada correctamente.');

        return back();
    }

    public function updateRazorpay(Request $request)
    {
        $validated = $request->validate([
            'razorpay_key' => 'required|string|max:500',
            'razorpay_secret' => 'required|string|max:500',
            'razorpay_currency' => 'required|string|size:3',
        ]);

        foreach ($validated as $key => $value) {
            PaymentSetting::updateOrCreate(
                ['key' => $key],
                ['value' => PaymentSetting::encryptIfSensitive($key, $value)]
            );
        }

        Cache::forget('payment_settings');

        flash()->success('Configuración de Razorpay actualizada correctamente.');

        return back();
    }
}
