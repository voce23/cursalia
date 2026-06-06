<?php

namespace App\Providers;

use App\Services\CommissionSettingService;
use Illuminate\Support\ServiceProvider;

class CommissionSettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            CommissionSettingService::setGlobalSettings();
        } catch (\Throwable $e) {
            // The table may not exist before running migrations.
        }
    }
}
