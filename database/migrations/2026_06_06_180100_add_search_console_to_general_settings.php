<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Verificación de propiedad para los motores de búsqueda.
 *
 *   Google Search Console → meta name="google-site-verification" content="…"
 *   Bing Webmaster Tools  → meta name="msvalidate.01" content="…"
 *
 * El admin pega solo el VALOR del `content`; nosotros emitimos el meta tag completo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('google_site_verification')->nullable()->after('seo_default_description');
            $table->string('bing_site_verification')->nullable()->after('google_site_verification');
            // Plus: ID de Google Analytics 4 (formato G-XXXXXXXXX) — para medir tráfico orgánico.
            $table->string('google_analytics_id', 40)->nullable()->after('bing_site_verification');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['google_site_verification', 'bing_site_verification', 'google_analytics_id']);
        });
    }
};
