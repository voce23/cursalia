<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Añade cabeceras de seguridad a todas las respuestas web.
 * No incluye una CSP estricta de scripts/estilos para no romper Alpine.js,
 * los handlers inline (@click) ni las fuentes de Google; sí protege contra
 * clickjacking, MIME-sniffing y fuga de referer.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (isset($response->headers)) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            $response->headers->set('X-DNS-Prefetch-Control', 'on');

            // CSP en 2 niveles:
            //   - Local/dev: solo anti-clickjacking (no rompe Alpine inline ni CDNs).
            //   - Producción: CSP estricta que permite los CDNs que usamos
            //     (cdnjs para FA/Prism, Google Tag Manager si hay GA) y bloquea
            //     todo lo demás. object-src none = anti Flash/PDF embebido.
            if (app()->environment('production')) {
                $response->headers->set('Content-Security-Policy', implode('; ', [
                    "default-src 'self'",
                    // 'unsafe-eval' es OBLIGATORIO para Alpine.js (evalúa x-data/@click
                    // con new Function()). Sin él, todo lo interactivo deja de funcionar
                    // (banner de cookies, menús, medidores de contraseña…).
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com",
                    "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
                    "img-src 'self' data: blob: https:",
                    "font-src 'self' https://cdnjs.cloudflare.com data:",
                    "connect-src 'self' https://www.google-analytics.com",
                    "frame-ancestors 'self'",
                    "object-src 'none'",
                    "base-uri 'self'",
                    "form-action 'self'",
                ]));
            } else {
                $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
            }

            if ($request->secure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
        }

        return $response;
    }
}
