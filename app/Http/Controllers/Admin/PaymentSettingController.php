<?php

namespace App\Http\Controllers\Admin;

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
        return view('admin.payment-settings.index', compact('settings'));
    }

    public function updatePaypal(Request $request)
    {
        $validated = $request->validate([
            'paypal_mode'          => 'required|in:sandbox,live',
            'paypal_client_id'     => 'required|string|max:500',
            'paypal_client_secret' => 'required|string|max:500',
            'paypal_currency'      => 'required|string|size:3',
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
        $validated = $request->validate([
            'stripe_publishable_key' => 'required|string|max:500',
            'stripe_secret'          => 'required|string|max:500',
            'stripe_currency'        => 'required|string|size:3',
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
            'razorpay_key'      => 'required|string|max:500',
            'razorpay_secret'   => 'required|string|max:500',
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
