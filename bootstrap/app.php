<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/stripe',
            'webhooks/paypal',
            'webhooks/razorpay',
        ]);

        // Cabeceras de seguridad en todas las respuestas web
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'is.admin'      => \App\Http\Middleware\IsAdmin::class,
            'is.instructor' => \App\Http\Middleware\IsInstructor::class,
            'is.student'    => \App\Http\Middleware\IsStudent::class,
            // Feature flags Cursalia (Fase 2: pagos, marketplace, certificados).
            'feature'       => \App\Http\Middleware\FeatureFlag::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.dashboard');
            }
            $user = $request->user();
            if ($user?->role === 'instructor') {
                return $user->approve_status === 'approved'
                    ? '/instructor/dashboard'
                    : '/instructor/pending';
            }
            return '/student/dashboard';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (AuthorizationException|AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson() || config('app.debug')) {
                return null;
            }

            return response()->view('errors.500', [], 500);
        });
    })->create();
