<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('contact_title')->default('Contacto');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('bottom_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('footers')->insert([
            'description' => 'Plataforma de cursos online con los mejores instructores de Bolivia y Latinoamérica.',
            'contact_title' => 'Contacto',
            'email' => 'info@lmsl13.test',
            'phone' => '+591 7000 0000',
            'address' => 'La Paz, Bolivia',
            'bottom_text' => 'Hecho con amor en Bolivia',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};