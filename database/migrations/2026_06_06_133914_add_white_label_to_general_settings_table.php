<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade campos de marca/white-label a general_settings.
 *
 * Permite que el admin pueda personalizar TODO sin tocar código:
 * paleta de colores (4 tokens), tipografías, preset guardado, secciones
 * del home a mostrar/ocultar, idioma por defecto y meta SEO de fallback.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('brand_color', 16)->nullable()->after('favicon');
            $table->string('accent_color', 16)->nullable()->after('brand_color');
            $table->string('sun_color', 16)->nullable()->after('accent_color');
            $table->string('ink_color', 16)->nullable()->after('sun_color');
            $table->string('font_display', 80)->nullable()->after('ink_color');
            $table->string('font_body', 80)->nullable()->after('font_display');
            $table->string('theme_preset', 40)->nullable()->after('font_body');
            $table->string('default_locale', 8)->nullable()->after('theme_preset');
            $table->string('seo_default_description', 320)->nullable()->after('default_locale');
            $table->string('og_image', 255)->nullable()->after('seo_default_description');
            $table->json('enabled_sections')->nullable()->after('og_image');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'brand_color', 'accent_color', 'sun_color', 'ink_color',
                'font_display', 'font_body', 'theme_preset', 'default_locale',
                'seo_default_description', 'og_image', 'enabled_sections',
            ]);
        });
    }
};
