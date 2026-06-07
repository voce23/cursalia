<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware feature-flag.
 *
 * Uso:
 *   Route::middleware('feature:payments')->group(function () {
 *       Route::get('/checkout', ...);
 *   });
 *
 * Si config('cursalia.payments.enabled') es false → 404.
 *
 * Útil para Fase 2: las rutas de pagos/marketplace/certificados ya existen
 * pero solo se sirven cuando el flag está activo (CURSALIA_PAYMENTS_ENABLED=true
 * en .env, luego php artisan config:cache).
 */
class FeatureFlag
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $enabled = config("cursalia.{$feature}.enabled", false);

        abort_if(! $enabled, 404, 'Esta funcionalidad no está disponible.');

        return $next($request);
    }
}
