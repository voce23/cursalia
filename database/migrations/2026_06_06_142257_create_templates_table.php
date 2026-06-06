<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_category_id')->nullable()
                ->constrained('template_categories')->nullOnDelete();

            $table->string('title', 120);
            $table->string('slug', 140)->unique();
            $table->string('headline', 200)->nullable();
            $table->longText('description')->nullable();

            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();

            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->nullable();
            $table->boolean('is_free')->default(false);

            $table->string('demo_url')->nullable();
            $table->string('download_url')->nullable();
            $table->string('version', 20)->default('1.0.0');

            $table->json('tech_stack')->nullable();
            $table->json('features')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedInteger('sales_count')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_free']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
