<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('name', 120)->nullable();
            $table->string('notes', 500)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
            $table->unique(['template_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_waitlists');
    }
};
