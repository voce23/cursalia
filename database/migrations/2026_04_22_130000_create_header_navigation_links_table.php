<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_navigation_links', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('url');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('open_in_new_tab')->default(false);
            $table->timestamps();
        });

        DB::table('header_navigation_links')->insert([
            ['title' => 'Inicio', 'url' => '/', 'sort_order' => 1, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Cursos', 'url' => '/courses', 'sort_order' => 2, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Blog', 'url' => '#', 'sort_order' => 3, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Nosotros', 'url' => '/about', 'sort_order' => 4, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Contacto', 'url' => '/contact', 'sort_order' => 5, 'is_active' => true, 'open_in_new_tab' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('header_navigation_links');
    }
};
