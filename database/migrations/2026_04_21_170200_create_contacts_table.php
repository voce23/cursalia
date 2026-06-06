<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('line_one')->nullable();
            $table->string('line_two')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('contacts')->insert([
            [
                'icon' => 'fa-solid fa-envelope',
                'title' => 'Correo Electrónico',
                'line_one' => 'info@lmsl13.test',
                'line_two' => 'soporte@lmsl13.test',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'fa-solid fa-phone',
                'title' => 'Teléfono',
                'line_one' => '+591 7000 0000',
                'line_two' => '+591 7000 1111',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'fa-solid fa-location-dot',
                'title' => 'Dirección',
                'line_one' => 'La Paz, Bolivia',
                'line_two' => 'Zona Sur',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
