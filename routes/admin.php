<?php

/*
|--------------------------------------------------------------------------
| Rutas Admin · Cursalia · FASE 1 (FREE)
|--------------------------------------------------------------------------
|
| Las rutas de pago (orders, payment_settings, commissions, withdraws,
| instructor_payment_gateways) NO se incluyen aquí — pertenecen a FASE 2.
| Los CRUDs visibles ahora son los mínimos para administrar Cursalia FREE.
|
*/

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->as('admin.')->group(function () {

    // ── Login admin (guest) ──────────────────────────────────────────────
    Route::get('/login',  [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:6,1');

    // ── Zona protegida (guard admin) ─────────────────────────────────────
    Route::middleware('is.admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Perfil
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/update-password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');

        // CRUD demo: Categorías de curso (patrón para los otros CRUDs)
        Route::resource('course-categories', CourseCategoryController::class)->except(['show']);

        // ── Cursos (constructor del admin) ────────────────────────────────────
        Route::get('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [\App\Http\Controllers\Admin\CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [\App\Http\Controllers\Admin\CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [\App\Http\Controllers\Admin\CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'update'])->name('courses.update');
        Route::patch('/courses/{course}/approval', [\App\Http\Controllers\Admin\CourseController::class, 'updateApproval'])->name('courses.approval');
        Route::delete('/courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');

        // ── Constructor de contenido (capítulos + lecciones) ──────────────────
        Route::get('/courses/{course}/content', [\App\Http\Controllers\Admin\CourseContentController::class, 'index'])->name('courses.content');
        Route::post('/courses/{course}/chapters', [\App\Http\Controllers\Admin\CourseContentController::class, 'storeChapter'])->name('chapters.store');
        Route::put('/chapters/{chapter}', [\App\Http\Controllers\Admin\CourseContentController::class, 'updateChapter'])->name('chapters.update');
        Route::delete('/chapters/{chapter}', [\App\Http\Controllers\Admin\CourseContentController::class, 'destroyChapter'])->name('chapters.destroy');
        Route::post('/chapters/{chapter}/move/{direction}', [\App\Http\Controllers\Admin\CourseContentController::class, 'moveChapter'])->name('chapters.move');
        Route::post('/chapters/{chapter}/lessons', [\App\Http\Controllers\Admin\CourseContentController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{lesson}', [\App\Http\Controllers\Admin\CourseContentController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [\App\Http\Controllers\Admin\CourseContentController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('/lessons/{lesson}/move/{direction}', [\App\Http\Controllers\Admin\CourseContentController::class, 'moveLesson'])->name('lessons.move');

        // ── Mensajes de contacto ──────────────────────────────────────────────
        Route::get('/messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/toggle-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'toggleRead'])->name('messages.toggle');
        Route::delete('/messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // ── Apariencia (white-label) ──────────────────────────────────────────
        Route::get('/appearance', [\App\Http\Controllers\Admin\AppearanceController::class, 'edit'])->name('appearance.edit');
        Route::post('/appearance', [\App\Http\Controllers\Admin\AppearanceController::class, 'update'])->name('appearance.update');
        Route::post('/appearance/preset', [\App\Http\Controllers\Admin\AppearanceController::class, 'applyPreset'])->name('appearance.preset');

        // ── Plantillas (marketplace) ──────────────────────────────────────────
        Route::get('/templates/waitlist', [\App\Http\Controllers\Admin\TemplateController::class, 'waitlist'])->name('templates.waitlist');
        Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);

        // ── Servicios (planes + bandeja de pedidos) ───────────────────────────
        Route::get('/services/requests', [\App\Http\Controllers\Admin\ServiceController::class, 'requests'])->name('services.requests');
        Route::patch('/services/requests/{serviceRequest}', [\App\Http\Controllers\Admin\ServiceController::class, 'updateRequestStatus'])->name('services.requests.update');
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)->except(['show']);

        // ── Blog (artículos + categorías + comentarios) ───────────────────────
        Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->except(['show']);
        Route::get('/blog-comments', [\App\Http\Controllers\Admin\BlogCommentController::class, 'index'])->name('blog-comments.index');
        Route::post('/blog-comments/{blogComment}/approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'approve'])->name('blog-comments.approve');
        Route::delete('/blog-comments/{blogComment}', [\App\Http\Controllers\Admin\BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');
        Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);

        // ── Quizzes · autoevaluaciones por lección (Cursalia FREE) ────────────
        Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class)->except(['show']);

        // ── Navegación · menú primario ────────────────────────────────────────
        Route::get('/navigation',  [\App\Http\Controllers\Admin\NavigationController::class, 'edit'])->name('navigation.edit');
        Route::post('/navigation', [\App\Http\Controllers\Admin\NavigationController::class, 'store'])->name('navigation.store');
        Route::patch('/navigation/{link}', [\App\Http\Controllers\Admin\NavigationController::class, 'update'])->name('navigation.update');
        Route::delete('/navigation/{link}', [\App\Http\Controllers\Admin\NavigationController::class, 'destroy'])->name('navigation.destroy');
        Route::post('/navigation/reorder', [\App\Http\Controllers\Admin\NavigationController::class, 'reorder'])->name('navigation.reorder');
        Route::post('/navigation/{link}/toggle', [\App\Http\Controllers\Admin\NavigationController::class, 'toggle'])->name('navigation.toggle');
    });
});
