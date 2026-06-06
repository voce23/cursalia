<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('featured_category_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->unsignedSmallInteger('limit_items')->default(10);
            $table->timestamps();
        });

        DB::table('featured_category_sections')->insert([
            'title' => 'Explora por Categoría',
            'subtitle' => 'Encuentra el curso perfecto para ti entre nuestras áreas de conocimiento',
            'limit_items' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_category_sections');
    }
};
