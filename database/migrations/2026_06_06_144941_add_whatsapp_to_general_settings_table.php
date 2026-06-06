<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('whatsapp_number', 32)->nullable()->after('og_image');
            $table->string('whatsapp_default_message', 255)->nullable()->after('whatsapp_number');
            $table->string('services_email', 120)->nullable()->after('whatsapp_default_message');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_number', 'whatsapp_default_message', 'services_email']);
        });
    }
};
