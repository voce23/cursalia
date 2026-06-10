<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->timestamps();
        });

        DB::table('about_sections')->insert([
            'title' => 'Sobre Nuestra Plataforma',
            'subtitle' => 'Aprendizaje práctico con resultados reales',
            'content' => '<p>Somos una plataforma enfocada en cursos de alta calidad, con instructores expertos y contenido actualizado para tu crecimiento profesional.</p>',
            'button_text' => 'Conoce Más',
            'button_url' => '/about',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_sections');
    }
};
