<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('email');
            $table->string('whatsapp', 32)->nullable();
            $table->string('contact_preference', 16)->default('email');
            $table->string('budget', 32)->nullable();
            $table->string('subject', 200)->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'contacted', 'in_progress', 'closed'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();
            $table->index(['status', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
