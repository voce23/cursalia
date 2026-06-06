<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

/**
 * Captcha matemático sencillo: "¿Cuánto es 3 + 5?".
 *
 * No depende de sesión (sin race conditions). La respuesta correcta viaja
 * cifrada en un hidden field; al validar se descifra y compara con la
 * respuesta del usuario.
 *
 * Uso típico:
 *   $c = MathCaptcha::generate();          // ['question' => '3 + 5', 'token' => '...']
 *   // En la vista: input con name="captcha_token" value="{{ $c['token'] }}"
 *   // y input con name="captcha_answer".
 *   if (! MathCaptcha::verify($req->captcha_token, $req->captcha_answer)) {
 *       return back()->withErrors(['captcha_answer' => 'Respuesta incorrecta.']);
 *   }
 */
class MathCaptcha
{
    /** Genera una operación nueva y devuelve la pregunta + el token cifrado. */
    public static function generate(): array
    {
        $operations = ['+', '-', '×'];
        $op = $operations[array_rand($operations)];

        $a = random_int(1, 10);
        $b = random_int(1, 10);

        // Asegurar resultado positivo en la resta
        if ($op === '-' && $b > $a) {
            [$a, $b] = [$b, $a];
        }

        $answer = match ($op) {
            '+' => $a + $b,
            '-' => $a - $b,
            '×' => $a * $b,
        };

        // Cifrar con expiración: payload con TTL para evitar reuso eterno
        $payload = json_encode([
            'a' => (int) $answer,
            't' => time() + 1800, // válido 30 minutos
        ]);

        return [
            'question' => "{$a} {$op} {$b}",
            'token'    => Crypt::encryptString($payload),
        ];
    }

    /** Devuelve true si la respuesta coincide con la cifrada en el token. */
    public static function verify(?string $token, mixed $answer): bool
    {
        if (! $token || $answer === null || $answer === '') {
            return false;
        }
        try {
            $raw = Crypt::decryptString($token);
            $data = json_decode($raw, true);
            if (! is_array($data) || ! isset($data['a'], $data['t'])) {
                return false;
            }
            if ($data['t'] < time()) {
                return false; // token expirado
            }
            return (int) $answer === (int) $data['a'];
        } catch (\Throwable) {
            return false;
        }
    }
}
