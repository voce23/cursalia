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
        Schema::create('instructor_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Ej: "PayPal Personal"
            $table->string('type');                     // paypal | bank_transfer | stripe_connect | other
            $table->text('instructions')->nullable();   // Instrucciones para el instructor
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_payment_gateways');
    }
};
