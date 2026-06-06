<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * ¿La clave guarda un secreto que debe cifrarse en reposo?
     * (paypal_client_secret, stripe_secret, razorpay_secret…)
     */
    public static function isSensitive(string $key): bool
    {
        return str_contains($key, 'secret');
    }

    /** Cifra el valor si la clave es sensible (para guardar en BD). */
    public static function encryptIfSensitive(string $key, ?string $value): ?string
    {
        if (self::isSensitive($key) && filled($value)) {
            return Crypt::encryptString($value);
        }

        return $value;
    }

    /** Descifra el valor si la clave es sensible; tolera valores legados en texto plano. */
    public static function decryptIfSensitive(string $key, ?string $value): ?string
    {
        if (self::isSensitive($key) && filled($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable) {
                return $value; // valor antiguo sin cifrar
            }
        }

        return $value;
    }
}
