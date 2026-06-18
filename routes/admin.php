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

use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogCommentController;
use App\Http\Controllers\Admin\LessonCommentController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ContactCardController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\CourseContentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FooterColumnOneController;
use App\Http\Controllers\Admin\FooterColumnTwoController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\HeaderSettingController;
use App\Http\Controllers\Admin\HomeMiscSectionController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\InstructorRequestController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\TemplateImportController;
use App\Http\Controllers\Admin\EditorImageController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->as('admin.')->group(function () {

    // ── Login admin (guest) ──────────────────────────────────────────────
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:6,1');

    // ── Zona protegida (guard admin) ─────────────────────────────────────
    Route::middleware('is.admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Ayuda · guía de uso del panel (sin tocar código)
        Route::view('/ayuda', 'admin.help')->name('help');

        // Perfil
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/update-password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');

        // ── Usuarios ──────────────────────────────────────────────────────
        // Estudiantes / usuarios (listar, ver, activar/desactivar)
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/crear', [UserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/usuarios/{user}/estado', [UserController::class, 'toggleStatus'])->name('users.toggle');

        // Solicitudes de instructor (aprobar / rechazar / descargar documento)
        Route::get('/instructores', [InstructorRequestController::class, 'index'])->name('instructor-requests.index');
        Route::put('/instructores/{instructor_request}', [InstructorRequestController::class, 'update'])->name('instructor-requests.update');
        Route::get('/instructores/{user}/documento', [InstructorRequestController::class, 'download'])->name('instructor-requests.download');

        // Administradores (superadmins de la plataforma)
        Route::get('/administradores', [AdministratorController::class, 'index'])->name('admins.index');
        Route::get('/administradores/crear', [AdministratorController::class, 'create'])->name('admins.create');
        Route::post('/administradores', [AdministratorController::class, 'store'])->name('admins.store');
        Route::get('/administradores/{admin}/editar', [AdministratorController::class, 'edit'])->name('admins.edit');
        Route::put('/administradores/{admin}', [AdministratorController::class, 'update'])->name('admins.update');
        Route::delete('/administradores/{admin}', [AdministratorController::class, 'destroy'])->name('admins.destroy');

        // CRUD demo: Categorías de curso (patrón para los otros CRUDs)
        Route::resource('course-categories', CourseCategoryController::class)->except(['show']);

        // ── Cursos (constructor del admin) ────────────────────────────────────
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::patch('/courses/{course}/approval', [CourseController::class, 'updateApproval'])->name('courses.approval');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

        // ── Constructor de contenido (capítulos + lecciones) ──────────────────
        Route::get('/courses/{course}/content', [CourseContentController::class, 'index'])->name('courses.content');
        Route::post('/courses/{course}/chapters', [CourseContentController::class, 'storeChapter'])->name('chapters.store');
        Route::put('/chapters/{chapter}', [CourseContentController::class, 'updateChapter'])->name('chapters.update');
        Route::delete('/chapters/{chapter}', [CourseContentController::class, 'destroyChapter'])->name('chapters.destroy');
        Route::post('/chapters/{chapter}/move/{direction}', [CourseContentController::class, 'moveChapter'])->name('chapters.move');
        Route::post('/chapters/{chapter}/lessons', [CourseContentController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{lesson}', [CourseContentController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [CourseContentController::class, 'destroyLesson'])->name('lessons.destroy');
        Route::post('/lessons/{lesson}/move/{direction}', [CourseContentController::class, 'moveLesson'])->name('lessons.move');

        // ── Mensajes de contacto ──────────────────────────────────────────────
        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/toggle-read', [ContactMessageController::class, 'toggleRead'])->name('messages.toggle');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // ── Apariencia (white-label) ──────────────────────────────────────────
        Route::get('/appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');
        Route::post('/appearance', [AppearanceController::class, 'update'])->name('appearance.update');
        Route::post('/appearance/preset', [AppearanceController::class, 'applyPreset'])->name('appearance.preset');

        // Pasarelas de pago (Stripe + PayPal + QR + Transferencia) · activable por llave
        Route::get('/pagos', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('payment-settings.index');
        Route::post('/pagos/activar', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'activate'])->name('payment-settings.activate');
        Route::post('/pagos/stripe', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateStripe'])->name('payment-settings.stripe');
        Route::post('/pagos/paypal', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updatePaypal'])->name('payment-settings.paypal');
        Route::post('/pagos/qr', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateQr'])->name('payment-settings.qr');
        Route::post('/pagos/transferencia', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateTransfer'])->name('payment-settings.transfer');

        // Ventas de cursos (aprobar/rechazar pagos manuales: QR y transferencia)
        Route::get('/ventas', [\App\Http\Controllers\Admin\CourseOrderController::class, 'index'])->name('course-orders.index');
        Route::post('/ventas/{order}/aprobar', [\App\Http\Controllers\Admin\CourseOrderController::class, 'approve'])->name('course-orders.approve');
        Route::post('/ventas/{order}/rechazar', [\App\Http\Controllers\Admin\CourseOrderController::class, 'reject'])->name('course-orders.reject');

        // ── Plantillas (marketplace) ──────────────────────────────────────────
        Route::get('/templates/waitlist', [TemplateController::class, 'waitlist'])->name('templates.waitlist');
        Route::get('/templates/import', [TemplateImportController::class, 'form'])->name('templates.import.form');
        Route::post('/templates/import', [TemplateImportController::class, 'import'])->name('templates.import');
        Route::post('/editor/image', [EditorImageController::class, 'store'])->name('editor.image');
        Route::resource('templates', TemplateController::class)->except(['show']);

        // ── Servicios (planes + bandeja de pedidos) ───────────────────────────
        Route::get('/services/requests', [ServiceController::class, 'requests'])->name('services.requests');
        Route::patch('/services/requests/{serviceRequest}', [ServiceController::class, 'updateRequestStatus'])->name('services.requests.update');
        Route::resource('services', ServiceController::class)->except(['show']);

        // ── Blog (artículos + categorías + comentarios) ───────────────────────
        Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);
        Route::get('/blog-comments', [BlogCommentController::class, 'index'])->name('blog-comments.index');
        Route::post('/blog-comments/{blogComment}/approve', [BlogCommentController::class, 'approve'])->name('blog-comments.approve');
        Route::delete('/blog-comments/{blogComment}', [BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');

        Route::get('/lesson-comments', [LessonCommentController::class, 'index'])->name('lesson-comments.index');
        Route::post('/lesson-comments/{lessonComment}/approve', [LessonCommentController::class, 'approve'])->name('lesson-comments.approve');
        Route::delete('/lesson-comments/{lessonComment}', [LessonCommentController::class, 'destroy'])->name('lesson-comments.destroy');
        Route::resource('blogs', BlogController::class)->except(['show']);

        // ── Quizzes · autoevaluaciones por lección (Cursalia FREE) ────────────
        Route::resource('quizzes', QuizController::class)->except(['show']);

        // ── Navegación · menú primario ────────────────────────────────────────
        Route::get('/navigation', [NavigationController::class, 'edit'])->name('navigation.edit');
        Route::post('/navigation', [NavigationController::class, 'store'])->name('navigation.store');
        Route::patch('/navigation/{link}', [NavigationController::class, 'update'])->name('navigation.update');
        Route::delete('/navigation/{link}', [NavigationController::class, 'destroy'])->name('navigation.destroy');
        Route::post('/navigation/reorder', [NavigationController::class, 'reorder'])->name('navigation.reorder');
        Route::post('/navigation/{link}/toggle', [NavigationController::class, 'toggle'])->name('navigation.toggle');

        // ── Pie de página (white-label) ───────────────────────────────────────
        Route::get('/footer', [FooterController::class, 'index'])->name('footer.index');
        Route::post('/footer', [FooterController::class, 'update'])->name('footer.update');
        Route::resource('footer-column-one', FooterColumnOneController::class)->except(['show']);
        Route::resource('footer-column-two', FooterColumnTwoController::class)->except(['show']);
        Route::resource('social-links', SocialLinkController::class)->except(['show']);

        // ── Página de contacto (ajustes + tarjetas) ───────────────────────────
        Route::get('/contact-settings', [ContactSettingController::class, 'index'])->name('contact-settings.index');
        Route::post('/contact-settings', [ContactSettingController::class, 'update'])->name('contact-settings.update');
        Route::resource('contact-cards', ContactCardController::class)->except(['show']);

        // ── Testimonios + Cifras (home / nosotros) ────────────────────────────
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::get('/counter', [CounterController::class, 'index'])->name('counter.index');
        Route::post('/counter', [CounterController::class, 'update'])->name('counter.update');

        // ── Secciones del inicio (hero, razones, categorías, cursos, about) ───
        Route::get('/home-sections', [HomeSectionController::class, 'index'])->name('home-sections.index');
        Route::post('/home-sections/hero', [HomeSectionController::class, 'updateHero'])->name('home-sections.hero');
        Route::post('/home-sections/features', [HomeSectionController::class, 'updateFeatures'])->name('home-sections.features');
        Route::post('/home-sections/featured-categories', [HomeSectionController::class, 'updateFeaturedCategories'])->name('home-sections.featured-categories');
        Route::post('/home-sections/latest-courses', [HomeSectionController::class, 'updateLatestCourses'])->name('home-sections.latest-courses');
        Route::post('/home-sections/about', [HomeSectionController::class, 'updateAbout'])->name('home-sections.about');
        Route::post('/home-misc', [HomeMiscSectionController::class, 'update'])->name('home-misc.update');

        // ── Marcas + Páginas personalizadas + Cabecera ────────────────────────
        Route::resource('brands', BrandController::class)->except(['show']);
        Route::resource('custom-pages', CustomPageController::class)->except(['show']);
        Route::get('/header-settings', [HeaderSettingController::class, 'index'])->name('header-settings.index');
        Route::post('/header-settings', [HeaderSettingController::class, 'update'])->name('header-settings.update');

        // ── Newsletter (suscriptores + envío) ─────────────────────────────────
        Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
        Route::post('/newsletter/send', [NewsletterController::class, 'send'])->name('newsletter.send');
        Route::delete('/newsletter/{subscriber}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');
    });
});
