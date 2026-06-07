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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('course_chapter_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // % mínimo para pasar
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('allow_retakes')->default(true);
            $table->integer('max_attempts')->default(3);
            $table->integer('time_limit')->nullable(); // en minutos
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
