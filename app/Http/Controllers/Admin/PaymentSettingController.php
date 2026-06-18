<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivationKey;
use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
    /** Métodos del paquete y si son automáticos o manuales. */
    public const METHODS = [
        'stripe' => ['label' => 'Tarjeta (Stripe)', 'auto' => true],
        'paypal' => ['label' => 'PayPal', 'auto' => true],
        'qr' => ['label' => 'QR', 'auto' => false],
        'transfer' => ['label' => 'Transferencia bancaria', 'auto' => false],
    ];

    public function index()
    {
        $settings = PaymentSetting::pluck('value', 'key')
            ->map(fn ($value, $key) => PaymentSetting::decryptIfSensitive($key, $value));

        return view('admin.payment-settings.index', [
            'settings' => $settings,
            'paymentsActive' => self::isActive(),
        ]);
    }

    /** Todos los ajustes (sin descifrar), cacheados para evitar N+1 en cada render. */
    private static function raw(): array
    {
        return Cache::rememberForever('payment_settings', fn () => PaymentSetting::pluck('value', 'key')->all());
    }

    /** ¿Está activo el complemento de pasarelas de pago? (llave PAY válida). */
    public static function isActive(): bool
    {
        $key = (string) (self::raw()['payments_key'] ?? '');

        return $key !== '' && ActivationKey::validate($key, 'PAY');
    }

    /** ¿Un método concreto está encendido (y el complemento activo)? */
    public static function methodEnabled(string $method): bool
    {
        return self::isActive()
            && (string) (self::raw()[$method.'_enabled'] ?? '') === '1';
    }

    /** Métodos encendidos (para mostrar en el checkout del curso). */
    public static function enabledMethods(): array
    {
        if (! self::isActive()) {
            return [];
        }

        return array_values(array_filter(
            array_keys(self::METHODS),
            fn ($m) => self::methodEnabled($m)
        ));
    }

    /** Activa el complemento con la llave PAY (la que entrega cursalia.org). */
    public function activate(Request $request)
    {
        $request->validate(['payments_key' => 'required|string|max:160']);

        // No se hace strtoupper: la llave es case-sensitive (base64url).
        $key = trim((string) $request->input('payments_key'));
        if (! ActivationKey::validate($key, 'PAY')) {
            return back()->withErrors(['payments_key' => 'La llave de pagos no es válida. Consíguela en cursalia.org/tienda.']);
        }

        PaymentSetting::updateOrCreate(['key' => 'payments_key'], ['value' => $key]);
        Cache::forget('payment_settings');
        flash()->success('¡Pasarelas de pago activadas! Ahora enciende y configura los métodos que quieras.');

        return back();
    }

    private function guardActive()
    {
        if (! self::isActive()) {
            return back()->withErrors(['payments_key' => 'Primero activa las pasarelas con tu llave.']);
        }

        return null;
    }

    private function save(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            PaymentSetting::updateOrCreate(
                ['key' => $key],
                ['value' => PaymentSetting::encryptIfSensitive($key, $value)]
            );
        }
        Cache::forget('payment_settings');
    }

    public function updateStripe(Request $request)
    {
        if ($r = $this->guardActive()) {
            return $r;
        }

        $v = $request->validate([
            'stripe_publishable_key' => 'nullable|string|max:500',
            'stripe_secret' => 'nullable|string|max:500',
            'stripe_currency' => 'required|string|size:3',
        ]);
        $v['stripe_enabled'] = $request->boolean('stripe_enabled') ? '1' : '0';

        $this->save($v);
        flash()->success('Stripe (tarjeta) actualizado.');

        return back();
    }

    public function updatePaypal(Request $request)
    {
        if ($r = $this->guardActive()) {
            return $r;
        }

        $v = $request->validate([
            'paypal_mode' => 'required|in:sandbox,live',
            'paypal_client_id' => 'nullable|string|max:500',
            'paypal_client_secret' => 'nullable|string|max:500',
            'paypal_currency' => 'required|string|size:3',
        ]);
        $v['paypal_enabled'] = $request->boolean('paypal_enabled') ? '1' : '0';

        $this->save($v);
        flash()->success('PayPal actualizado.');

        return back();
    }

    public function updateQr(Request $request)
    {
        if ($r = $this->guardActive()) {
            return $r;
        }

        $request->validate([
            'qr_holder' => 'nullable|string|max:160',
            'qr_instructions' => 'nullable|string|max:600',
            'qr_image' => 'nullable|image|max:4096',
        ]);

        $pairs = [
            'qr_holder' => (string) $request->input('qr_holder'),
            'qr_instructions' => (string) $request->input('qr_instructions'),
            'qr_enabled' => $request->boolean('qr_enabled') ? '1' : '0',
        ];

        if ($request->hasFile('qr_image')) {
            // borra el anterior si existía
            $old = PaymentSetting::where('key', 'qr_image')->value('value');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $pairs['qr_image'] = $request->file('qr_image')->store('payments', 'public');
        }

        $this->save($pairs);
        flash()->success('Pago con QR actualizado.');

        return back();
    }

    public function updateTransfer(Request $request)
    {
        if ($r = $this->guardActive()) {
            return $r;
        }

        $v = $request->validate([
            'transfer_bank' => 'nullable|string|max:160',
            'transfer_account' => 'nullable|string|max:160',
            'transfer_holder' => 'nullable|string|max:160',
            'transfer_instructions' => 'nullable|string|max:600',
            'manual_currency' => 'nullable|string|max:3',
        ]);
        $v['manual_currency'] = strtoupper($v['manual_currency'] ?? '') ?: 'USD';
        $v['transfer_enabled'] = $request->boolean('transfer_enabled') ? '1' : '0';

        $this->save($v);
        flash()->success('Transferencia bancaria actualizada.');

        return back();
    }
}
