<?php

namespace App\Services;

use App\Models\PaymentSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PaymentGatewaySettingService
{
    public static function getSettings(): Collection
    {
        // Se cachea el valor crudo (cifrado en reposo) y se descifra al usarse.
        $raw = Cache::rememberForever('payment_settings', function () {
            return PaymentSetting::pluck('value', 'key');
        });

        return $raw->map(fn ($value, $key) => PaymentSetting::decryptIfSensitive($key, $value));
    }

    public static function setGlobalSettings(): void
    {
        $settings = self::getSettings();

        // PayPal
        $mode = $settings->get('paypal_mode', 'sandbox');
        config()->set('paypal.mode', $mode);
        config()->set('paypal.sandbox.client_id', $settings->get('paypal_client_id', ''));
        config()->set('paypal.sandbox.client_secret', $settings->get('paypal_client_secret', ''));
        config()->set('paypal.live.client_id', $settings->get('paypal_client_id', ''));
        config()->set('paypal.live.client_secret', $settings->get('paypal_client_secret', ''));
        config()->set('paypal.currency', $settings->get('paypal_currency', 'USD'));

        // Stripe
        config()->set('stripe.publishable_key', $settings->get('stripe_publishable_key', ''));
        config()->set('stripe.secret', $settings->get('stripe_secret', ''));
        config()->set('stripe.currency', $settings->get('stripe_currency', 'USD'));

        // Razorpay
        config()->set('razorpay.key', $settings->get('razorpay_key', ''));
        config()->set('razorpay.secret', $settings->get('razorpay_secret', ''));
        config()->set('razorpay.currency', $settings->get('razorpay_currency', 'INR'));
    }
}
