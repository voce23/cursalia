<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_misc_sections', function (Blueprint $table) {
            $table->id();
            $table->string('newsletter_title')->nullable();
            $table->string('newsletter_subtitle')->nullable();
            $table->string('instructor_banner_title')->nullable();
            $table->string('instructor_banner_subtitle')->nullable();
            $table->string('instructor_banner_button_text')->nullable();
            $table->string('instructor_banner_button_url')->nullable();
            $table->string('video_section_title')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
        });

        DB::table('home_misc_sections')->insert([
            'newsletter_title' => 'Suscríbete a nuestro newsletter',
            'newsletter_subtitle' => 'Recibe novedades de cursos y ofertas en tu correo.',
            'instructor_banner_title' => '¿Quieres ser instructor?',
            'instructor_banner_subtitle' => 'Comparte tu conocimiento y genera ingresos.',
            'instructor_banner_button_text' => 'Empieza a Enseñar',
            'instructor_banner_button_url' => '/student/become-instructor',
            'video_section_title' => 'Conoce nuestra plataforma',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_misc_sections');
    }
};
