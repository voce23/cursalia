<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Si Cursalia aún no está instalado (no hay .env), llevar al instalador web
// (estilo WordPress). Se hace ANTES de arrancar Laravel para no fallar por
// falta de .env / APP_KEY. Una instalación existente siempre tiene .env, así
// que esto solo afecta a un paquete recién subido.
if (! file_exists(__DIR__.'/../.env') && file_exists(__DIR__.'/install.php')) {
    header('Location: install.php');
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
