<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use App\Models\Withdraw;
use App\Policies\CourseChapterLessonPolicy;
use App\Policies\CourseChapterPolicy;
use App\Policies\CoursePolicy;
use App\Policies\WithdrawPolicy;
use App\View\Composers\BrandingComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // En producción forzar HTTPS para que todos los asset_url y
        // generated URLs usen https:// automáticamente
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Usar vistas de paginación con clases Tailwind
        // (el proyecto usa Tailwind 4 en todos los paneles)
        Paginator::useTailwind();

        // BrandingComposer global: comparte $generalSetting, $headerLinks,
        // $socialLinks, $footerInfo, $footerColumnOne/Two, $legalPages con
        // TODAS las vistas. Esto deja Cursalia 100% white-label sin tocar
        // código (todo se edita desde admin).
        View::composer('*', BrandingComposer::class);

        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(CourseChapter::class, CourseChapterPolicy::class);
        Gate::policy(CourseChapterLesson::class, CourseChapterLessonPolicy::class);
        Gate::policy(Withdraw::class, WithdrawPolicy::class);

        RateLimiter::for('payments', function ($request) {
            return [
                Limit::perMinute(5)->by('payments|user|' . (optional($request->user())->id ?: $request->ip())),
                Limit::perMinute(20)->by('payments|ip|' . $request->ip()),
            ];
        });

        RateLimiter::for('auth-sensitive', function ($request) {
            $identity = (string) $request->input('email') . '|' . $request->ip();

            return Limit::perMinute(10)->by('auth|' . $identity);
        });
    }
}

