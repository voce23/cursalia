<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('latest_course_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->unsignedSmallInteger('limit_items')->default(4);
            $table->timestamps();
        });

        DB::table('latest_course_sections')->insert([
            'title' => 'Cursos Destacados',
            'subtitle' => 'Los más populares entre nuestros estudiantes',
            'limit_items' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('latest_course_sections');
    }
};
