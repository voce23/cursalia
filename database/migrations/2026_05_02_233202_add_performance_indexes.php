<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users: role y approve_status se usan constantemente en WHERE/COUNT
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('approve_status');
            $table->index(['role', 'approve_status']);
        });

        // courses: is_approved y status son los filtros más frecuentes del proyecto
        Schema::table('courses', function (Blueprint $table) {
            $table->index('is_approved');
            $table->index('status');
            $table->index(['is_approved', 'status']);
            $table->index('created_at');
        });

        // orders: created_at para las queries MONTH()/YEAR() del dashboard
        Schema::table('orders', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });

        // blogs: status y published_at para listados y filtros del frontend
        Schema::table('blogs', function (Blueprint $table) {
            $table->index('status');
            $table->index('published_at');
            $table->index(['status', 'published_at']);
        });

        // course_chapters: instructor_id para el dashboard del instructor
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->index('instructor_id');
            $table->index('status');
        });

        // course_chapter_lessons: course_id y status para filtros de lecciones
        Schema::table('course_chapter_lessons', function (Blueprint $table) {
            $table->index('status');
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['approve_status']);
            $table->dropIndex(['role', 'approve_status']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_approved', 'status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['status', 'published_at']);
        });

        Schema::table('course_chapters', function (Blueprint $table) {
            $table->dropIndex(['instructor_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('course_chapter_lessons', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['course_id']);
        });
    }
};
