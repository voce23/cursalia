<?php

namespace App\Providers;

use App\Services\PaymentGatewaySettingService;
use Illuminate\Support\ServiceProvider;

class PaymentGatewaySettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            PaymentGatewaySettingService::setGlobalSettings();
        } catch (\Throwable $e) {
            // La tabla puede no existir antes de migrar, o el caché puede estar corrupto
        }
    }
}
