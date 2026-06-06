<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0)->after('price');
            $table->decimal('platform_earning', 10, 2)->default(0)->after('commission_rate');
            $table->decimal('instructor_earning', 10, 2)->default(0)->after('platform_earning');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'platform_earning', 'instructor_earning']);
        });
    }
};
