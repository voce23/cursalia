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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay', 'short_answer'])->default('multiple_choice');
            $table->integer('order')->default(0);
            $table->boolean('required')->default(true);
            $table->integer('points')->default(1);
            $table->text('explanation')->nullable(); // se muestra después de responder
            $table->timestamps();

            $table->index('quiz_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
