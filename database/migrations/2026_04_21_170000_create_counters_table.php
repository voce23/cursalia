<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('counter_one_title')->nullable();
            $table->unsignedInteger('counter_one_value')->default(0);
            $table->string('counter_two_title')->nullable();
            $table->unsignedInteger('counter_two_value')->default(0);
            $table->string('counter_three_title')->nullable();
            $table->unsignedInteger('counter_three_value')->default(0);
            $table->string('counter_four_title')->nullable();
            $table->unsignedInteger('counter_four_value')->default(0);
            $table->timestamps();
        });

        DB::table('counters')->insert([
            'counter_one_title' => 'Cursos',
            'counter_one_value' => 500,
            'counter_two_title' => 'Instructores',
            'counter_two_value' => 85,
            'counter_three_title' => 'Alumnos',
            'counter_three_value' => 12000,
            'counter_four_title' => 'Horas de Contenido',
            'counter_four_value' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
