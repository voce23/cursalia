<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('email');
            $table->string('headline')->nullable()->after('image');
            $table->text('bio')->nullable()->after('headline');
            $table->enum('gender', ['male', 'female'])->nullable()->after('bio');
            $table->string('phone')->nullable()->after('gender');
            $table->string('facebook')->nullable()->after('phone');
            $table->string('x')->nullable()->after('facebook');
            $table->string('linkedin')->nullable()->after('x');
            $table->string('website')->nullable()->after('linkedin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['image', 'headline', 'bio', 'gender', 'phone', 'facebook', 'x', 'linkedin', 'website']);
        });
    }
};
