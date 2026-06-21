<?php

namespace App\Helpers;

use App\Models\ProSetting;
use Illuminate\Support\Facades\Cache;

/**
 * Estado de la licencia PRO de Cursalia (modelo "Divi"): UNA llave con
 * prefijo PRO desbloquea TODOS los complementos PRO (Migrador, etc.).
 *
 * La llave la genera cursalia.org con su clave PRIVADA; aquí solo se
 * VERIFICA con la clave pública (offline, sin API). Ver App\Helpers\ActivationKey.
 */
class Pro
{
    /** Prefijo de las llaves PRO. */
    public const PREFIX = 'PRO';

    /** Llave PRO guardada (o cadena vacía). Cacheada para no consultar en cada render. */
    public static function rawKey(): string
    {
        $all = Cache::rememberForever('pro_settings', fn () => ProSetting::pluck('value', 'key')->all());

        return (string) ($all['pro_key'] ?? '');
    }

    /** ¿Hay una licencia PRO válida activada? */
    public static function isActive(): bool
    {
        $key = self::rawKey();

        return $key !== '' && ActivationKey::validate($key, self::PREFIX);
    }

    /** Guarda la llave PRO (debe venir ya validada). */
    public static function store(string $key): void
    {
        ProSetting::updateOrCreate(['key' => 'pro_key'], ['value' => trim($key)]);
        self::forget();
    }

    /** Limpia la caché de ajustes PRO. */
    public static function forget(): void
    {
        Cache::forget('pro_settings');
    }
}
