<?php

namespace App\Helpers;

/**
 * Valida (offline, sin API) las llaves de activación gratuitas que entrega
 * cursalia.org (ej. botón de WhatsApp). Usa el MISMO secreto compartido
 * (config cursalia.activation_secret) que cursalia-web.
 *
 * Formato: PREFIJO-XXXXXXXX-YYYY  (ej. WA-A3B7K9P2-1F4E)
 */
class ActivationKey
{
    /** Valida una llave para un complemento (por su prefijo). */
    public static function validate(string $key, string $prefix = 'WA'): bool
    {
        $prefix = strtoupper($prefix);
        $key = strtoupper(trim($key));

        if (! preg_match('/^([A-Z0-9]+)-([A-Z0-9]{8})-([A-Z0-9]{4})$/', $key, $m)) {
            return false;
        }
        if ($m[1] !== $prefix) {
            return false;
        }

        return hash_equals(self::signature($m[1], $m[2]), $m[3]);
    }

    private static function signature(string $prefix, string $core): string
    {
        $secret = (string) config('cursalia.activation_secret');

        return strtoupper(substr(hash_hmac('sha256', $prefix.':'.$core, $secret), 0, 4));
    }
}
