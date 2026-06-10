<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_bars', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('offer_text')->nullable();
            $table->string('offer_url')->nullable();
            $table->string('background_color', 20)->default('#111827');
            $table->string('text_color', 20)->default('#d1d5db');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('top_bars')->insert([
            'email' => 'info@lmsl13.test',
            'phone' => '+591 7000 0000',
            'offer_text' => '🎓 ¡50% de descuento en tu primer curso!',
            'offer_url' => null,
            'background_color' => '#111827',
            'text_color' => '#d1d5db',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('top_bars');
    }
};
