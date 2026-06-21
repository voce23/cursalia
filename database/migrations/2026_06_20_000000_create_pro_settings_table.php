<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajustes de los complementos PRO de Cursalia (clave-valor).
 * Aquí vive la llave PRO que desbloquea complementos como el Migrador.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_settings');
    }
};
