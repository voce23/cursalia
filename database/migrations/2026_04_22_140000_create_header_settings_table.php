<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category_button_text')->default('Categorías');
            $table->unsignedTinyInteger('category_limit')->default(6);
            $table->boolean('show_search')->default(true);
            $table->string('search_placeholder', 120)->default('Buscar cursos...');
            $table->timestamps();
        });

        DB::table('header_settings')->insert([
            'category_button_text' => 'Categorías',
            'category_limit' => 6,
            'show_search' => true,
            'search_placeholder' => 'Buscar cursos...',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('header_settings');
    }
};
