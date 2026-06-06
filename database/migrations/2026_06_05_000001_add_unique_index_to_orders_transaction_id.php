<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Idempotencia a nivel de BD: evita órdenes duplicadas por el mismo
            // transaction_id (webhook + redirect concurrentes). NULL no cuenta como
            // duplicado en MySQL, así que no afecta a órdenes sin transacción.
            $table->unique('transaction_id', 'orders_transaction_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_transaction_id_unique');
        });
    }
};
