<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Botón flotante de WhatsApp: activación (switch) + llave de activación.
 * El número y el mensaje ya existen (whatsapp_number, whatsapp_default_message).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('whatsapp_enabled')->default(false)->after('whatsapp_default_message');
            $table->string('whatsapp_key', 40)->nullable()->after('whatsapp_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_enabled', 'whatsapp_key']);
        });
    }
};
