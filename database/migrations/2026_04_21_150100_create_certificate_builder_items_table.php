<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_builder_items', function (Blueprint $table) {
            $table->id();
            $table->string('element_id')->unique();
            $table->unsignedInteger('x_position')->default(0);
            $table->unsignedInteger('y_position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_builder_items');
    }
};
