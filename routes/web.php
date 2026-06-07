<?php

/*
|--------------------------------------------------------------------------
| Rutas web · Cursalia · FASE 1 (FREE)
|--------------------------------------------------------------------------
|
| En FASE 1 NO hay pagos: carrito, checkout, pasarelas y webhooks quedan
| comentados (sus controladores existen para que en FASE 2 sea solo
| activar el bloque correspondiente).
|
*/

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\EnrolledCourseController as StudentEnrolledCourseController;
use App\Http\Controllers\Student\CoursePlayerController as StudentCoursePlayerController;
use App\Http\Controllers\Student\LessonCompletionController as StudentLessonCompletionController;
use App\Http\Controllers\Frontend\QuizController as FrontendQuizController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Home Cursalia (frontal real con CMS) ─────────────────────────────────────
Route::get('/', [\App\Http\Controllers\Frontend\CoursePageController::class, 'home'])->name('home');

// ── SEO: sitemap.xml dinámico (cacheado 1h, controller dedicado) ────────────
Route::get('/sitemap.xml', \App\Http\Controllers\SitemapController::class)->name('sitemap');

// ── SEO: robots.txt dinámico con URL de sitemap ABSOLUTA ────────────────────
//    (servido por Laravel para que el dominio se resuelva automáticamente
//     en cualquier entorno; el estándar pide URL absoluta en la directiva).
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin/',
        'Disallow: /student/',
        'Disallow: /instructor/',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /forgot-password',
        'Disallow: /reset-password',
        'Allow: /',
        '',
        'Sitemap: '.url('/sitemap.xml'),
    ];

    return response(implode("\n", $lines)."\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

// ── Catálogo de cursos (Sprint 4) ────────────────────────────────────────────
Route::get('/courses', [\App\Http\Controllers\Frontend\CoursePageController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [\App\Http\Controllers\Frontend\CoursePageController::class, 'show'])->name('courses.show');

// Inscripción GRATUITA (solo cursos con price=0). Pagos = FASE 2.
Route::post('/courses/{course:slug}/enroll-free', [\App\Http\Controllers\Frontend\FreeEnrollmentController::class, 'store'])
    ->middleware(['auth', 'throttle:10,1'])
    ->name('courses.enroll-free');

// ── Nosotros / Contacto (Sprint 5) ───────────────────────────────────────────
Route::get('/about',   [\App\Http\Controllers\Frontend\SitePageController::class, 'about'])->name('about');
Route::get('/contact', [\App\Http\Controllers\Frontend\SitePageController::class, 'contact'])->name('contact');
// E-E-A-T: página pública del autor del blog (clave para SEO).
Route::get('/sobre-el-autor', [\App\Http\Controllers\Frontend\SitePageController::class, 'author'])->name('author');
Route::post('/contact', [\App\Http\Controllers\Frontend\SitePageController::class, 'sendContact'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

// ── Blog (Sprint 5) ──────────────────────────────────────────────────────────
Route::get('/blog', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{blog}/comments', [\App\Http\Controllers\Frontend\BlogCommentController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('blog.comments.store');

Route::get('/instructors/{username}', fn ($u) => view('soon', [
    'title' => 'Perfil de instructor',
    'description' => 'El perfil público de @'.$u.' estará disponible pronto.',
]))->name('instructors.show');

// ── Servicios y Asesoría (Sprint 7.10) ──────────────────────────────────────
Route::get('/services',  [\App\Http\Controllers\Frontend\ServiceController::class, 'index'])->name('services.index');
Route::post('/services/request', [\App\Http\Controllers\Frontend\ServiceController::class, 'storeRequest'])
    ->middleware('throttle:5,1')->name('services.request');

// ── Marketplace de plantillas (Sprint 7.9) ──────────────────────────────────
Route::get('/templates',  [\App\Http\Controllers\Frontend\TemplateMarketplaceController::class, 'index'])->name('templates.index');
Route::get('/templates/{slug}', [\App\Http\Controllers\Frontend\TemplateMarketplaceController::class, 'show'])->name('templates.show');
Route::post('/templates/{slug}/waitlist', [\App\Http\Controllers\Frontend\TemplateMarketplaceController::class, 'joinWaitlist'])
    ->middleware('throttle:6,1')->name('templates.waitlist');
Route::post('/templates/{slug}/download', [\App\Http\Controllers\Frontend\TemplateMarketplaceController::class, 'download'])
    ->middleware('throttle:10,1')->name('templates.download');

// ── Páginas legales (Cursalia) ───────────────────────────────────────────────
Route::get('/legal/{slug}', [\App\Http\Controllers\Frontend\LegalPageController::class, 'show'])
    ->where('slug', 'privacy|terms|data-deletion|refunds')
    ->name('legal');

Route::post('/newsletter/subscribe', [\App\Http\Controllers\Frontend\NewsletterSubscribeController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('newsletter.subscribe');

// ── Rutas de invitado (login / register / reset) ─────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ── Usuario autenticado (logout + verificación de email) ─────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        $user = $request->user();
        if ($user->role === 'instructor') {
            return $user->approve_status === 'approved'
                ? redirect()->intended('/instructor/dashboard')
                : redirect('/instructor/pending');
        }
        return redirect()->intended('/student/dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/instructor/pending', fn () => view('instructor.pending'))->name('instructor.pending');
});

// ── Alumno ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'is.student'])
    ->prefix('student')
    ->as('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [StudentProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/update-password', [StudentProfileController::class, 'updatePassword'])->name('profile.update-password');

        // ── Mis cursos + reproductor + progreso (Cursalia FREE) ──────────────
        Route::get('/enrolled-courses', [StudentEnrolledCourseController::class, 'index'])->name('enrolled-courses.index');

        Route::get('/learn/{course:slug}', [StudentCoursePlayerController::class, 'show'])->name('player.show');
        Route::post('/learn/{course:slug}/lessons/{lesson}/toggle-complete', [StudentLessonCompletionController::class, 'toggle'])
            ->name('player.lesson.toggle-complete');

        // ── Quiz · autoevaluación mínima FREE (sin certificado) ──────────────
        Route::post('/quiz/{quiz}/submit', [FrontendQuizController::class, 'submit'])
            ->middleware('throttle:20,1')
            ->name('quiz.submit');
    });

// ── Instructor (mínimo: dashboard) ──────────────────────────────────────────
Route::middleware(['auth', 'is.instructor'])
    ->prefix('instructor')
    ->as('instructor.')
    ->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    });

// ══════════════════════════════════════════════════════════════════════════════
// FASE 2 (PRO) — todavía SIN activar. Lo siguiente se habilita cuando
// instalemos stripe/razorpay y rediseñemos las vistas:
//   · Frontend público: /courses, /blog, /about, /contact, /instructors
//   · Reproductor del alumno: /student/learn/...
//   · Carrito y checkout: /cart, /checkout
//   · Pasarelas: /paypal/*, /stripe/*, /razorpay/*
//   · Webhooks: /webhooks/stripe|paypal|razorpay
//   · File manager para instructores
//   · Newsletter, comentarios de blog, reseñas
// Sus controladores ya viven en app/Http/Controllers/.
// ══════════════════════════════════════════════════════════════════════════════
