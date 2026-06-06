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
        Schema::create('watch_histories', function (Blueprint $table) {
              $table->id();
              $table->foreignId('user_id')->constrained()->cascadeOnDelete();
              $table->foreignId('course_id')->constrained()->cascadeOnDelete();
              $table->unsignedBigInteger('lesson_id');
              $table->foreign('lesson_id')->references('id')->on('course_chapter_lessons')->cascadeOnDelete();
              $table->timestamps();
              $table->unique(['user_id', 'course_id']); // una fila por usuario+curso
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
