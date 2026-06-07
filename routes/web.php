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
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Home Cursalia (frontal real con CMS) ─────────────────────────────────────
Route::get('/', [\App\Http\Controllers\Frontend\CoursePageController::class, 'home'])->name('home');

// ── SEO: sitemap.xml dinámico (cacheado 1h para no pegar a BD en cada hit) ──
Route::get('/sitemap.xml', function () {
    // Cache 1h. Si publicas/editas un post o curso y necesitas invalidar antes,
    // ejecuta: php artisan cache:forget cursalia.sitemap
    $xml = \Illuminate\Support\Facades\Cache::remember('cursalia.sitemap', 3600, function () {
        $urls = collect([
        ['loc' => url('/'),                  'priority' => '1.0', 'freq' => 'daily'],
        ['loc' => url('/courses'),           'priority' => '0.9', 'freq' => 'daily'],
        ['loc' => url('/about'),             'priority' => '0.7', 'freq' => 'monthly'],
        ['loc' => url('/sobre-el-autor'),    'priority' => '0.8', 'freq' => 'monthly'],
        ['loc' => url('/contact'),           'priority' => '0.5', 'freq' => 'monthly'],
        ['loc' => url('/blog'),              'priority' => '0.8', 'freq' => 'weekly'],
        // Hub del curso — entrada SEO importante.
        ['loc' => url('/blog?category=curso-cursalia'), 'priority' => '0.9', 'freq' => 'weekly'],
        ['loc' => url('/register'),          'priority' => '0.6', 'freq' => 'yearly'],
        ['loc' => url('/login'),             'priority' => '0.4', 'freq' => 'yearly'],
    ]);

    // Cursos publicados
    \App\Models\Course::query()
        ->where('is_approved', 'approved')
        ->where('status', 'active')
        ->select('slug', 'updated_at')
        ->get()
        ->each(fn ($c) => $urls->push([
            'loc'      => url('/courses/'.$c->slug),
            'priority' => '0.8',
            'freq'     => 'weekly',
            'lastmod'  => $c->updated_at?->toAtomString(),
        ]));

    // Posts del blog
    \App\Models\Blog::query()
        ->where('status', 'published')
        ->whereNotNull('published_at')
        ->select('slug', 'updated_at')
        ->get()
        ->each(fn ($b) => $urls->push([
            'loc'      => url('/blog/'.$b->slug),
            'priority' => '0.7',
            'freq'     => 'monthly',
            'lastmod'  => $b->updated_at?->toAtomString(),
        ]));

        return view('sitemap', ['urls' => $urls])->render();
    });

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

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

Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $r) {
    $r->validate(['email' => 'required|email']);
    \App\Models\NewsletterSubscriber::firstOrCreate(
        ['email' => strtolower($r->email)],
        ['is_active' => true]
    );
    return back()->with('status', '¡Gracias por suscribirte! 💚');
})->middleware('throttle:6,1')->name('newsletter.subscribe');

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
