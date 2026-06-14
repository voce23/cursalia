<?php

/**
 * Punto de entrada para el montaje en "public_html" (2 carpetas).
 *
 * Cuando subes el paquete completo dentro de public_html (dominio principal),
 * este archivo recibe la petición y muestra el instalador, que se encargará de
 * separar la app a una carpeta hermana (p. ej. midominio_app) por seguridad.
 *
 * En el montaje estándar (document root → /public) este archivo NO se sirve,
 * y tras instalar es reemplazado por el index.php definitivo.
 */
$base = __DIR__;

if (is_file($base . '/public/install.php')) {
    require $base . '/public/install.php';
    exit;
}

// Fallback defensivo (no debería alcanzarse): intenta el front controller estándar.
if (is_file($base . '/public/index.php')) {
    require $base . '/public/index.php';
}
