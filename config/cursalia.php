<?php

/**
 * Configuración específica del producto Cursalia.
 *
 * Feature flags y constantes de negocio centralizadas aquí, NO hardcoded
 * en controllers o vistas. Para activar/desactivar funcionalidades:
 *
 *   - Cambia el .env y ejecuta `php artisan config:cache` (en producción).
 *   - Acceso desde código: config('cursalia.payments.enabled')
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    |
    | Activan/desactivan grandes módulos del producto. Útil para:
    |   - Lanzar fases gradualmente (Fase 1 sin pagos → Fase 2 con pagos).
    |   - Apagar un módulo si rompe, sin desplegar nuevo código.
    |
    */

    'payments' => [
        // Stripe + PayPal + checkout completo (Fase 2). En Fase 1 = false.
        'enabled' => (bool) env('CURSALIA_PAYMENTS_ENABLED', false),
    ],

    'marketplace' => [
        // Marketplace multi-instructor (Fase 2 PRO).
        'enabled' => (bool) env('CURSALIA_MARKETPLACE_ENABLED', false),
    ],

    'certificates' => [
        // Generación automática de PDFs de certificación (Fase 2 PRO).
        'enabled' => (bool) env('CURSALIA_CERTIFICATES_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding por defecto (fallback si BD no responde)
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'site_name' => 'Cursalia',
        'site_slogan' => 'Aprende algo nuevo, a tu manera',
        'brand_color' => '#10B981',
        'accent_color' => '#FB7185',
        'sun_color' => '#FBBF24',
        'ink_color' => '#1F2933',
    ],

    /*
    |--------------------------------------------------------------------------
    | Curso del blog
    |--------------------------------------------------------------------------
    */

    'course' => [
        'category_slug' => 'curso-cursalia',
        'lessons_free' => 14, // Fase 1 gratis
        'lessons_pro' => 12, // Fase 2 PRO
    ],

    /*
    |--------------------------------------------------------------------------
    | Llaves de activación de complementos gratuitos
    |--------------------------------------------------------------------------
    | Secreto compartido con cursalia.org para validar OFFLINE (sin API) las
    | llaves de activación (ej. botón de WhatsApp). DEBE ser idéntico al de
    | cursalia-web (config cursalia.activation_secret allá).
    */
    'activation_secret' => env('CURSALIA_ACTIVATION_SECRET', 'cursalia-llave-2026-x7K9pQ2mZ'),

];
