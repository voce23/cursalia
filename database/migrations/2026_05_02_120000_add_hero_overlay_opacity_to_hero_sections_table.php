<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->unsignedTinyInteger('hero_overlay_opacity')->default(55)->after('hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('hero_sections', function (Blueprint $table) {
            $table->dropColumn('hero_overlay_opacity');
        });
    }
};
