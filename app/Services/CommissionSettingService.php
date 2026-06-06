<?php

namespace App\Services;

use App\Models\CommissionSetting;
use Illuminate\Support\Facades\Cache;

class CommissionSettingService
{
    public static function instance(): CommissionSetting
    {
        return CommissionSetting::firstOrCreate(['id' => 1], [
            'commission_rate' => 20.00,
        ]);
    }

    public static function rate(): float
    {
        return Cache::rememberForever('commission_rate', function () {
            return (float) self::instance()->commission_rate;
        });
    }

    public static function setGlobalSettings(): void
    {
        config()->set('commission.rate', self::rate());
    }
}
