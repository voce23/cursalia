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

        // ── Navegación · menú primario ────────────────────────────────────────
        Route::get('/navigation',  [\App\Http\Controllers\Admin\NavigationController::class, 'edit'])->name('navigation.edit');
        Route::post('/navigation', [\App\Http\Controllers\Admin\NavigationController::class, 'store'])->name('navigation.store');
        Route::patch('/navigation/{link}', [\App\Http\Controllers\Admin\NavigationController::class, 'update'])->name('navigation.update');
        Route::delete('/navigation/{link}', [\App\Http\Controllers\Admin\NavigationController::class, 'destroy'])->name('navigation.destroy');
        Route::post('/navigation/reorder', [\App\Http\Controllers\Admin\NavigationController::class, 'reorder'])->name('navigation.reorder');
        Route::post('/navigation/{link}/toggle', [\App\Http\Controllers\Admin\NavigationController::class, 'toggle'])->name('navigation.toggle');
    });
});
