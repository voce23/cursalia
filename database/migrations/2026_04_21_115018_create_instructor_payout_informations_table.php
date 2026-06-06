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
        Schema::create('instructor_payout_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->nullable()->constrained('instructor_payment_gateways')->nullOnDelete();
            $table->string('account_name')->nullable();      // Nombre del titular
            $table->string('account_email')->nullable();     // Email (PayPal)
            $table->string('bank_name')->nullable();         // Nombre del banco
            $table->string('account_number')->nullable();    // Número de cuenta
            $table->string('routing_number')->nullable();    // Código de transferencia / IBAN
            $table->text('other_details')->nullable();       // Cualquier otra info relevante
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_payout_informations');
    }
};
