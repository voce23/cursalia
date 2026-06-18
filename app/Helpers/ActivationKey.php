<?php

namespace App\Helpers;

/**
 * Verifica (offline, sin API) las llaves de activación que entrega cursalia.org.
 *
 * Seguridad ASIMÉTRICA (Ed25519 / libsodium): este LMS SOLO lleva la clave PÚBLICA
 * (config cursalia.activation_public_key) y verifica. La clave PRIVADA que firma las
 * llaves vive únicamente en cursalia.org y NO se distribuye, así que tener este código
 * no permite fabricar llaves válidas.
 *
 * Formato: PREFIJO-<base64url(nonce[6] + firma[64])>
 */
class ActivationKey
{
    private const NONCE_BYTES = 6;

    /** Verifica una llave para un complemento (por su prefijo). */
    public static function validate(string $key, string $prefix = 'PAY'): bool
    {
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $prefix)) ?: 'PAY';
        $key = trim($key);

        $dash = strpos($key, '-');
        if ($dash === false || substr($key, 0, $dash) !== $prefix) {
            return false;
        }

        $body = substr($key, $dash + 1);
        $blob = base64_decode(strtr($body, '-_', '+/'), true);
        $canonical = $blob === false ? '' : rtrim(strtr(base64_encode($blob), '+/', '-_'), '=');
        if ($blob === false
            || strlen($blob) !== self::NONCE_BYTES + SODIUM_CRYPTO_SIGN_BYTES
            || $canonical !== $body) { // exige codificación canónica
            return false;
        }

        $nonce = substr($blob, 0, self::NONCE_BYTES);
        $sig = substr($blob, self::NONCE_BYTES);
        $message = $prefix.':'.bin2hex($nonce);
        $publicKey = base64_decode((string) config('cursalia.activation_public_key'));

        try {
            return sodium_crypto_sign_verify_detached($sig, $message, $publicKey);
        } catch (\SodiumException $e) {
            return false;
        }
    }
}
