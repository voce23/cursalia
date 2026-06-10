<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('LMSL13');
            $table->string('site_slogan')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('copyright')->nullable();
            $table->timestamps();
        });

        // Fila única inicial (patrón "single-row settings")
        DB::table('general_settings')->insert([
            'site_name' => 'LMSL13',
            'site_slogan' => 'Cursos Online',
            'copyright' => '© '.date('Y').' LMSL13. Todos los derechos reservados.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
