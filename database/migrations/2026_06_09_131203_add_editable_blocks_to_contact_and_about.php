<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            // Horario de atención editable. Una línea por fila, formato "Etiqueta|Valor".
            $table->text('schedule')->nullable()->after('map_embed_url');
        });

        Schema::table('about_sections', function (Blueprint $table) {
            // Lista de valores de la página "Nosotros". Una línea por valor.
            $table->text('about_values')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->dropColumn('schedule');
        });

        Schema::table('about_sections', function (Blueprint $table) {
            $table->dropColumn('about_values');
        });
    }
};
