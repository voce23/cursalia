<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La columna `bio` del admin venía como varchar(255) del esquema original,
 * pero el form admite hasta 6000 caracteres (E-E-A-T quiere una bio rica).
 * La pasamos a TEXT (~65k) para que coincida con la validación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->text('bio')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('bio', 255)->nullable()->change();
        });
    }
};
