<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('title', 120);
            $table->string('headline', 200)->nullable();
            $table->longText('description')->nullable();
            $table->string('icon', 80)->nullable();      // fa-solid fa-…
            $table->string('color', 16)->default('#10B981');

            $table->decimal('price', 8, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->string('price_suffix', 40)->nullable(); // "por hora", "pago único", "/mes"
            $table->boolean('is_free')->default(false);

            $table->json('features')->nullable();        // ["...", "..."]
            $table->string('badge_text', 40)->nullable();   // "Recomendado", "Popular"

            $table->string('cta_text', 60)->default('Solicitar');
            $table->string('cta_url', 255)->nullable();     // si NULL → form de request

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
