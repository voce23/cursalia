<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pedidos de compra de un curso (complemento "Pasarelas de Pago").
 *
 * - Stripe / PayPal: se crean ya "approved" (el cobro es automático).
 * - QR / Transferencia: se crean "pending" con el comprobante subido por el alumno;
 *   el dueño los aprueba para dar acceso.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('method', 20);                 // stripe | paypal | qr | transfer
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 12)->default('pending'); // pending | approved | rejected
            $table->string('proof_path')->nullable();     // comprobante (QR/transfer)
            $table->string('transaction_id')->nullable();  // id de Stripe/PayPal
            $table->string('reference')->nullable();       // nota del alumno
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_orders');
    }
};
