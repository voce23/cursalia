<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SEO + E-E-A-T (Sprint 8 · SEO Google).
 *
 * Blogs   → control fino del snippet en Google (meta_title/description),
 *           imagen OG personalizada por post y FAQ para rich snippet de preguntas.
 * Admins  → headline + redes sociales oficiales para que el JSON-LD Person
 *           con sameAs[] le diga a Google "este autor existe y es real".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Si el admin no los rellena, el sistema cae al title/summary del post.
            $table->string('meta_title', 70)->nullable()->after('summary');
            $table->string('meta_description', 180)->nullable()->after('meta_title');
            // Imagen OG/Twitter específica del post (si difiere de la thumbnail).
            $table->string('og_image_custom')->nullable()->after('meta_description');
            // FAQ schema: [{q: '...', a: '...'}, ...]
            $table->json('faq')->nullable()->after('og_image_custom');
        });

        Schema::table('admins', function (Blueprint $table) {
            // image y bio ya existen; añadimos sólo lo que falta.
            $table->string('headline', 180)->nullable()->after('bio');
            $table->string('social_x')->nullable()->after('headline');
            $table->string('social_linkedin')->nullable()->after('social_x');
            $table->string('social_github')->nullable()->after('social_linkedin');
            $table->string('social_youtube')->nullable()->after('social_github');
            $table->string('social_web')->nullable()->after('social_youtube');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image_custom', 'faq']);
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['headline', 'social_x', 'social_linkedin', 'social_github', 'social_youtube', 'social_web']);
        });
    }
};
