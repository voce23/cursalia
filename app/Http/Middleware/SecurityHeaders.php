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
            // Anti-clickjacking a nivel CSP (no afecta scripts/estilos inline)
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

            if ($request->secure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
        }

        return $response;
    }
}
