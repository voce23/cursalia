<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_sections', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaults = [
            ['icon' => 'fa-solid fa-laptop-code', 'title' => 'Aprende Online', 'description' => 'Estudia desde cualquier lugar y a tu ritmo.', 'sort_order' => 1],
            ['icon' => 'fa-solid fa-user-graduate', 'title' => 'Instructores Expertos', 'description' => 'Cursos impartidos por profesionales activos.', 'sort_order' => 2],
            ['icon' => 'fa-solid fa-certificate', 'title' => 'Certificados', 'description' => 'Obtén certificados al finalizar tus cursos.', 'sort_order' => 3],
            ['icon' => 'fa-solid fa-mobile-screen', 'title' => 'Acceso Multiplataforma', 'description' => 'Aprende en desktop, tablet o móvil.', 'sort_order' => 4],
        ];

        foreach ($defaults as $item) {
            DB::table('feature_sections')->insert([
                ...$item,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_sections');
    }
};
