<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('title')->nullable();
            $table->string('highlight_text')->nullable();
            $table->text('description')->nullable();
            $table->string('primary_button_text')->nullable();
            $table->string('primary_button_url')->nullable();
            $table->string('secondary_button_text')->nullable();
            $table->string('secondary_button_url')->nullable();
            $table->string('hero_image')->nullable();
            $table->timestamps();
        });

        DB::table('hero_sections')->insert([
            'badge_text' => 'Plataforma #1 en Bolivia',
            'title' => 'Aprende las habilidades del',
            'highlight_text' => 'futuro',
            'description' => 'Más de 500 cursos impartidos por expertos. Desde programación hasta diseño, al ritmo que tú prefieras.',
            'primary_button_text' => 'Explorar Cursos',
            'primary_button_url' => '/courses',
            'secondary_button_text' => 'Soy Instructor',
            'secondary_button_url' => '/student/become-instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
