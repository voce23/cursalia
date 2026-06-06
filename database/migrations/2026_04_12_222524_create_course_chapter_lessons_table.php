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
        Schema::create('course_chapter_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->foreignId('instructor_id')->constrained('users');
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('chapter_id')->constrained('course_chapters')->cascadeOnDelete();
            $table->text('file_path')->nullable();
            $table->enum('storage', ['upload', 'youtube', 'vimeo', 'external_link'])->default('upload');
            $table->string('duration')->nullable();
            $table->enum('file_type', ['video', 'audio', 'doc', 'pdf', 'file'])->default('video');
            $table->boolean('downloadable')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_chapter_lessons');
    }
};
