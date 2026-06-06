<?php

namespace App\Providers;

use App\Services\GeneralSettingService;
use Illuminate\Support\ServiceProvider;

class GeneralSettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            GeneralSettingService::setGlobal();
        } catch (\Throwable $e) {
            // La tabla puede no existir antes de migrar — ignorar silenciosamente.
        }
    }
}
