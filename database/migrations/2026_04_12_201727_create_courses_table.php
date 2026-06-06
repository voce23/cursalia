<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $table->foreignId('course_level_id')->nullable()->constrained('course_levels')->nullOnDelete();
            $table->foreignId('course_language_id')->nullable()->constrained('course_languages')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('seo_description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('demo_video_storage')->nullable();
            $table->text('demo_video_source')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('discount', 8, 2)->nullable();
            $table->string('duration')->nullable();
            $table->boolean('certificate')->default(false);
            $table->boolean('qna')->default(false);
            $table->text('message_for_reviewer')->nullable();
            $table->string('is_approved')->default('pending');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
