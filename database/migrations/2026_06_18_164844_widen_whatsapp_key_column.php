<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Las llaves de activación pasaron a firma asimétrica (Ed25519), que son más
 * largas (~100 caracteres). Ensancha la columna que las guarda.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('general_settings', 'whatsapp_key')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->string('whatsapp_key', 160)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('general_settings', 'whatsapp_key')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->string('whatsapp_key', 40)->nullable()->change();
            });
        }
    }
};
