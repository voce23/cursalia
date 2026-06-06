<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('form_title')->nullable();
            $table->string('form_subtitle')->nullable();
            $table->string('receiver_email')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->timestamps();
        });

        DB::table('contact_settings')->insert([
            'title' => 'Contáctanos',
            'subtitle' => 'Estamos listos para ayudarte en tu proceso de aprendizaje.',
            'form_title' => 'Envíanos un mensaje',
            'form_subtitle' => 'Te responderemos lo antes posible.',
            'receiver_email' => 'info@lmsl13.test',
            'map_embed_url' => 'https://www.google.com/maps?q=La%20Paz%20Bolivia&output=embed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
