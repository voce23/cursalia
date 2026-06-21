<?php
/**
 * INSTALADOR DE CURSALIA (estilo Duplicator)
 * ------------------------------------------------------------------
 * Asistente web independiente para restaurar/clonar un sitio Cursalia
 * en un hosting nuevo a partir del paquete `migrador-paquete-*.zip`.
 *
 * Sube ESTE archivo + el paquete .zip a la carpeta pública del nuevo
 * dominio y abre  https://tudominio/instalador.php  en el navegador.
 *
 * No necesita Composer ni la línea de comandos: hace todo solo
 * (extraer archivos, importar la base de datos, escribir el .env y
 * dejar el sitio funcionando).
 *
 * Por seguridad, BORRA este archivo y el .zip cuando termines.
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
@set_time_limit(0);
@ini_set('memory_limit', '512M');

const MIN_PHP = '8.3.0';

$webRoot = __DIR__;
$accion  = $_POST['accion'] ?? ($_GET['accion'] ?? 'inicio');

/* ------------------------------------------------------------------ *
 *  Utilidades
 * ------------------------------------------------------------------ */

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function encontrarPaquete(string $dir): ?string
{
    $zips = glob($dir . '/migrador-paquete-*.zip') ?: [];
    if (! $zips) {
        return null;
    }
    usort($zips, fn ($a, $b) => filemtime($b) <=> filemtime($a));

    return $zips[0];
}

/**
 * Estima la carpeta HOME del hosting (cPanel: /home/USUARIO) para alojar el
 * código SIEMPRE fuera de cualquier carpeta pública. Funciona tanto para el
 * dominio principal (public_html) como para dominios adicionales, que usan
 * su propia carpeta y NO public_html.
 */
function homeDir(string $webRoot): string
{
    $p = str_replace('\\', '/', $webRoot);
    if (preg_match('#^(/home\d*/[^/]+)#', $p, $m)) {
        return $m[1];
    }

    return str_replace('\\', '/', dirname($webRoot));
}

/** Barra de progreso del asistente (4 pasos). */
function progreso(int $activo): void
{
    $pasos = ['Requisitos', 'Base de datos', 'Sitio y admin', 'Instalar'];
    echo '<div style="display:flex;gap:6px;margin:-4px 0 22px;">';
    foreach ($pasos as $i => $p) {
        $n = $i + 1;
        $on = $n <= $activo;
        echo '<div style="flex:1;text-align:center;">'
            . '<div style="height:5px;border-radius:3px;background:' . ($on ? '#10B981' : '#e2e8f0') . ';margin-bottom:6px;"></div>'
            . '<span style="font-size:11px;font-weight:700;color:' . ($on ? '#047857' : '#94a3b8') . ';">' . $n . '. ' . $p . '</span>'
            . '</div>';
    }
    echo '</div>';
}

function requisitos(string $webRoot): array
{
    $checks = [];
    $checks[] = [
        'level' => version_compare(PHP_VERSION, MIN_PHP, '>=') ? 'ok' : 'bad',
        'txt'   => 'PHP ' . MIN_PHP . ' o superior (tienes ' . PHP_VERSION . ')',
    ];
    $checks[] = ['level' => class_exists('ZipArchive') ? 'ok' : 'bad', 'txt' => 'Extensión ZIP'];
    $checks[] = ['level' => extension_loaded('pdo_mysql') ? 'ok' : 'bad', 'txt' => 'Extensión PDO MySQL'];
    $checks[] = ['level' => extension_loaded('mbstring') ? 'ok' : 'bad', 'txt' => 'Extensión mbstring'];
    $checks[] = ['level' => extension_loaded('openssl') ? 'ok' : 'bad', 'txt' => 'Extensión OpenSSL'];
    $checks[] = ['level' => is_writable($webRoot) ? 'ok' : 'bad', 'txt' => 'La carpeta es escribible'];
    $checks[] = ['level' => is_writable(dirname($webRoot)) ? 'ok' : 'bad', 'txt' => 'La carpeta superior es escribible (para el código)'];

    // Servidor web (AVISO, no bloquea): Apache/LiteSpeed usan .htaccess; Nginx no.
    $sw = strtolower((string) ($_SERVER['SERVER_SOFTWARE'] ?? ''));
    if ($sw !== '') {
        if (strpos($sw, 'apache') !== false || strpos($sw, 'litespeed') !== false) {
            $checks[] = ['level' => 'ok', 'txt' => 'Servidor compatible con .htaccess (Apache/LiteSpeed)'];
        } elseif (strpos($sw, 'nginx') !== false) {
            $checks[] = ['level' => 'warn', 'txt' => 'Servidor Nginx: el .htaccess podría NO aplicarse; el ruteo necesitaría configuración manual.'];
        }
    }

    // Memoria PHP (AVISO): armar/instalar paquetes grandes pide holgura.
    $mem = memoriaMb((string) ini_get('memory_limit'));
    if ($mem !== null) {
        if ($mem >= 0 && $mem < 256) {
            $checks[] = ['level' => 'warn', 'txt' => 'Memoria PHP baja (' . $mem . ' MB): paquetes grandes podrían ir justos (ideal 256 MB o más).'];
        } else {
            $checks[] = ['level' => 'ok', 'txt' => 'Memoria PHP: ' . ($mem < 0 ? 'sin límite' : $mem . ' MB')];
        }
    }

    return $checks;
}

/** Convierte memory_limit a MB (-1 = sin límite, null = desconocido). */
function memoriaMb(string $v): ?int
{
    $v = trim($v);
    if ($v === '') {
        return null;
    }
    if ($v === '-1') {
        return -1;
    }
    $u = strtolower(substr($v, -1));
    $n = (float) $v;
    if ($u === 'g') {
        return (int) ($n * 1024);
    }
    if ($u === 'm') {
        return (int) $n;
    }
    if ($u === 'k') {
        return (int) ($n / 1024);
    }

    return (int) ($n / 1048576);
}

/** Divide un volcado SQL en sentencias, respetando comillas y escapes. */
function dividirSql(string $sql): array
{
    $stmts = [];
    $buf = '';
    $len = strlen($sql);
    $inStr = false;      // carácter de comilla actual o false
    $esc = false;

    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $buf .= $c;

        if ($inStr !== false) {
            if ($esc) {
                $esc = false;
            } elseif ($c === '\\') {
                $esc = true;
            } elseif ($c === $inStr) {
                $inStr = false;
            }
            continue;
        }

        if ($c === '\'' || $c === '"') {
            $inStr = $c;
        } elseif ($c === ';') {
            $stmt = trim($buf);
            if ($stmt !== '' && $stmt !== ';') {
                $stmts[] = rtrim($stmt, ';');
            }
            $buf = '';
        }
    }

    $resto = trim($buf);
    if ($resto !== '') {
        $stmts[] = $resto;
    }

    return $stmts;
}

/** Reemplaza (o añade) una clave en el contenido de un .env. */
function ponerEnv(string $contenido, string $clave, string $valor): string
{
    // Entrecomillar si tiene caracteres especiales.
    if ($valor === '' || preg_match('/[\s#"\']/', $valor)) {
        $valor = '"' . str_replace('"', '\\"', $valor) . '"';
    }
    $linea = $clave . '=' . $valor;

    if (preg_match('/^' . preg_quote($clave, '/') . '=.*$/m', $contenido)) {
        return preg_replace('/^' . preg_quote($clave, '/') . '=.*$/m', $linea, $contenido);
    }

    return rtrim($contenido) . "\n" . $linea . "\n";
}

/** Copia recursiva de una carpeta (incluye archivos ocultos). */
function copiarDir(string $src, string $dst, array $omitir = []): void
{
    if (! is_dir($src)) {
        return;
    }
    @mkdir($dst, 0755, true);

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($items as $item) {
        $rel = ltrim(str_replace('\\', '/', substr($item->getPathname(), strlen($src))), '/');
        foreach ($omitir as $o) {
            if ($rel === $o || str_starts_with($rel, $o . '/')) {
                continue 2;
            }
        }
        $destino = $dst . '/' . $rel;
        if ($item->isDir()) {
            @mkdir($destino, 0755, true);
        } else {
            @mkdir(dirname($destino), 0755, true);
            @copy($item->getPathname(), $destino);
        }
    }
}

/* ------------------------------------------------------------------ *
 *  Cabecera/pie HTML
 * ------------------------------------------------------------------ */

function cabecera(string $titulo): void
{
    ?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Instalador de Cursalia · <?= h($titulo) ?></title>
<style>
  :root { --brand:#10B981; --brand-d:#047857; --ink:#1f2933; --bg:#f1f5f4; --soft:#64748b; --bad:#e11d48; --good:#059669; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; background:var(--bg); color:var(--ink); line-height:1.5; }
  .wrap { max-width:680px; margin:40px auto; padding:0 18px; }
  .card { background:#fff; border-radius:18px; box-shadow:0 8px 30px rgba(15,23,42,.07); overflow:hidden; }
  .head { background:linear-gradient(135deg,var(--brand),var(--brand-d)); color:#fff; padding:26px 30px; }
  .head h1 { margin:0; font-size:22px; font-weight:800; }
  .head p { margin:6px 0 0; color:#d1fae5; font-size:14px; }
  .body { padding:28px 30px; }
  h2 { font-size:17px; margin:0 0 14px; }
  ul.checks { list-style:none; padding:0; margin:0 0 18px; }
  ul.checks li { padding:9px 0; border-bottom:1px solid #eef2f1; display:flex; align-items:center; gap:10px; font-size:15px; }
  .pill { width:22px; height:22px; border-radius:50%; display:grid; place-items:center; color:#fff; font-size:13px; font-weight:700; flex:none; }
  .ok { background:var(--good); } .no { background:var(--bad); } .warn { background:#d97706; }
  label { display:block; font-weight:600; font-size:14px; margin:14px 0 5px; }
  input[type=text], input[type=password] { width:100%; padding:11px 13px; border:1px solid #cbd5e1; border-radius:10px; font-size:15px; }
  .row { display:flex; gap:12px; } .row > div { flex:1; }
  .hint { font-size:12.5px; color:var(--soft); margin:5px 0 0; }
  .check { display:flex; align-items:center; gap:9px; margin-top:14px; font-size:14px; }
  .btn { display:inline-flex; align-items:center; gap:8px; background:var(--ink); color:#fff; border:0; border-radius:999px; padding:13px 26px; font-size:15px; font-weight:700; cursor:pointer; text-decoration:none; }
  .btn.brand { background:var(--brand); } .btn:disabled { opacity:.5; cursor:not-allowed; }
  .note { background:#fffbeb; border:1px solid #fde68a; color:#92400e; border-radius:12px; padding:13px 15px; font-size:13.5px; margin:16px 0; }
  .err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:12px; padding:13px 15px; font-size:14px; margin:14px 0; }
  .okbox { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:12px; padding:13px 15px; font-size:14px; margin:14px 0; }
  .log { background:#0f172a; color:#cbd5e1; border-radius:12px; padding:16px; font-family:ui-monospace,Menlo,Consolas,monospace; font-size:13px; white-space:pre-wrap; margin:14px 0; max-height:340px; overflow:auto; }
  .log .g { color:#34d399; } .log .r { color:#fb7185; }
  code { background:#eef2f1; padding:1px 6px; border-radius:5px; font-size:13px; }
</style>
</head>
<body><div class="wrap"><div class="card">
  <div class="head"><h1>Instalador de Cursalia</h1><p><?= h($titulo) ?></p></div>
  <div class="body"><?php
}

function pie(): void
{
    ?></div></div>
  <p style="text-align:center;color:#94a3b8;font-size:12px;margin:18px 0;">Cursalia · Migrador estilo Duplicator</p>
  </div></body></html><?php
}

/* ------------------------------------------------------------------ *
 *  PASO: instalar (procesa el formulario)
 * ------------------------------------------------------------------ */

if ($accion === 'instalar') {
    cabecera('Paso 4 de 4 · Instalando');
    progreso(4);

    $log = [];
    $fail = function (string $msg) use (&$log) {
        $log[] = ['r', '✗ ' . $msg];
        echo '<h2>No se pudo completar</h2>';
        echo '<div class="log">';
        foreach ($log as [$cls, $t]) {
            echo '<span class="' . $cls . '">' . h($t) . "</span>\n";
        }
        echo '</div>';
        echo '<div class="err">Corrige el problema y vuelve a intentarlo. No se ha dejado el sitio a medias si fue en los primeros pasos.</div>';
        echo '<a class="btn" href="?accion=form">← Volver</a>';
        pie();
        exit;
    };
    $ok = function (string $msg) use (&$log) {
        $log[] = ['g', '✓ ' . $msg];
    };

    $dbHost = trim($_POST['db_host'] ?? '');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = (string) ($_POST['db_pass'] ?? '');
    $appUrl = rtrim(trim($_POST['app_url'] ?? ''), '/');
    $codeDir = rtrim(trim($_POST['code_dir'] ?? ''), '/\\');
    $vaciar = ! empty($_POST['vaciar']);
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass = (string) ($_POST['admin_pass'] ?? '');

    if ($dbHost === '' || $dbName === '' || $dbUser === '' || $appUrl === '' || $codeDir === '') {
        $fail('Faltan datos obligatorios del formulario.');
    }

    // 1) Conexión a la base de datos
    try {
        $pdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Throwable $e) {
        $fail('No se pudo conectar a MySQL: ' . $e->getMessage());
    }
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");
    } catch (Throwable $e) {
        $fail('No se pudo seleccionar/crear la base de datos: ' . $e->getMessage());
    }
    $ok('Conectado a la base de datos ' . $dbName);

    // 2) Localizar el paquete
    $paquete = encontrarPaquete(__DIR__);
    if ($paquete === null) {
        $fail('No encontré el paquete (migrador-paquete-*.zip) junto a este instalador.');
    }
    $ok('Paquete encontrado: ' . basename($paquete));

    $zip = new ZipArchive();
    if ($zip->open($paquete) !== true) {
        $fail('No pude abrir el paquete ZIP.');
    }

    // 3) Extraer el código
    @mkdir($codeDir, 0755, true);
    if (! is_dir($codeDir) || ! is_writable($codeDir)) {
        $fail('No puedo escribir en la carpeta del código: ' . $codeDir);
    }
    $extraidos = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (! str_starts_with($name, 'codigo/')) {
            continue;
        }
        $rel = substr($name, strlen('codigo/'));
        if ($rel === '') {
            continue;
        }
        $dest = $codeDir . '/' . $rel;
        if (str_ends_with($name, '/')) {
            @mkdir($dest, 0755, true);
            continue;
        }
        @mkdir(dirname($dest), 0755, true);
        $stream = $zip->getStream($name);
        if ($stream) {
            file_put_contents($dest, $stream);
            fclose($stream);
            $extraidos++;
        }
    }
    $ok("Código extraído ({$extraidos} archivos) en {$codeDir}");

    // 3b) Recrear carpetas de trabajo que NO van en el paquete (cachés/logs)
    foreach ([
        '/storage/framework/cache/data',
        '/storage/framework/sessions',
        '/storage/framework/views',
        '/storage/logs',
        '/bootstrap/cache',
    ] as $d) {
        @mkdir($codeDir . $d, 0755, true);
    }
    $ok('Carpetas de trabajo (storage, cachés) creadas');

    // 4) Extraer uploads al web root
    $extr = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (! str_starts_with($name, 'uploads/') || str_ends_with($name, '/')) {
            continue;
        }
        $dest = $webRoot . '/' . $name; // uploads/...
        @mkdir(dirname($dest), 0755, true);
        $stream = $zip->getStream($name);
        if ($stream) {
            file_put_contents($dest, $stream);
            fclose($stream);
            $extr++;
        }
    }
    $ok("Imágenes/subidas restauradas ({$extr} archivos)");

    // 5) Volcado SQL a string
    $sqlData = $zip->getFromName('database.sql');
    $zip->close();
    if ($sqlData === false) {
        $fail('El paquete no contiene database.sql.');
    }

    // Preservar el "candado" de versión de PHP del hosting (cPanel) que vive en
    // el .htaccess actual, ANTES de reemplazarlo, para que el sitio no vuelva a
    // una versión de PHP vieja tras la instalación.
    $phpHandler = '';
    if (is_file($webRoot . '/.htaccess')) {
        $htActual = (string) @file_get_contents($webRoot . '/.htaccess');
        if (preg_match('/# php -- BEGIN cPanel-generated handler.*?# php -- END cPanel-generated handler[^\r\n]*/s', $htActual, $mm)) {
            $phpHandler = trim($mm[0]) . "\n\n";
        }
    }

    // 6) Copiar los assets públicos al web root (sin index.php ni uploads/storage)
    copiarDir($codeDir . '/public', $webRoot, ['index.php', 'uploads', 'storage']);
    $ok('Recursos públicos (build, imágenes) copiados al sitio');

    // 7) index.php de arranque + .htaccess
    $codeAbs = str_replace('\\', '/', realpath($codeDir) ?: $codeDir);
    $indexPhp = "<?php\nuse Illuminate\\Foundation\\Application;\nuse Illuminate\\Http\\Request;\n"
        . "define('LARAVEL_START', microtime(true));\n"
        . "\$base = '" . addslashes($codeAbs) . "';\n"
        . "if (file_exists(\$m = \$base.'/storage/framework/maintenance.php')) { require \$m; }\n"
        . "require \$base.'/vendor/autoload.php';\n"
        . "\$app = require_once \$base.'/bootstrap/app.php';\n"
        . "\$app->usePublicPath(__DIR__);\n"
        . "\$app->handleRequest(Request::capture());\n";
    file_put_contents($webRoot . '/index.php', $indexPhp);
    // .htaccess de Laravel + (si lo había) el bloque de versión de PHP del hosting.
    $htPath = $webRoot . '/.htaccess';
    $htFinal = is_file($htPath) ? (string) file_get_contents($htPath) : htaccessLaravel();
    // Evitar que LiteSpeed cachee páginas: Cursalia muestra contenido por usuario
    // (sesión, "Hola, Administrador"), así que la caché de página NO debe usarse.
    if (strpos($htFinal, 'E=Cache-Control:no-cache') === false) {
        $htFinal = "<IfModule LiteSpeed>\nRewriteEngine On\nRewriteRule .* - [E=Cache-Control:no-cache]\n</IfModule>\n\n" . $htFinal;
    }
    if ($phpHandler !== '' && strpos($htFinal, 'cPanel-generated handler') === false) {
        $htFinal = $phpHandler . $htFinal;
    }
    file_put_contents($htPath, $htFinal);
    $ok('Arranque del sitio configurado (index.php + .htaccess' . ($phpHandler !== '' ? ' + PHP del hosting' : '') . ')');

    // 8) Enlace de storage (público)
    $linkStorage = $webRoot . '/storage';
    $targetStorage = $codeAbs . '/storage/app/public';
    if (! file_exists($linkStorage)) {
        if (! @symlink($targetStorage, $linkStorage)) {
            copiarDir($targetStorage, $linkStorage); // fallback: copia
        }
    }
    $ok('Enlace de almacenamiento público listo');

    // 9) Escribir el .env
    $envPath = $codeDir . '/.env';
    $env = is_file($envPath) ? file_get_contents($envPath) : "APP_NAME=Cursalia\n";
    $env = ponerEnv($env, 'APP_ENV', 'production');
    $env = ponerEnv($env, 'APP_DEBUG', 'false');
    $env = ponerEnv($env, 'APP_URL', $appUrl);
    $env = ponerEnv($env, 'DB_CONNECTION', 'mysql');
    $env = ponerEnv($env, 'DB_HOST', $dbHost);
    $env = ponerEnv($env, 'DB_PORT', $dbPort);
    $env = ponerEnv($env, 'DB_DATABASE', $dbName);
    $env = ponerEnv($env, 'DB_USERNAME', $dbUser);
    $env = ponerEnv($env, 'DB_PASSWORD', $dbPass);
    file_put_contents($envPath, $env);
    $ok('Archivo .env escrito (base de datos + dominio actualizados)');

    // 10) Importar la base de datos
    try {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        if ($vaciar) {
            foreach ($pdo->query('SHOW TABLES', PDO::FETCH_COLUMN, 0) as $t) {
                $pdo->exec("DROP TABLE IF EXISTS `{$t}`");
            }
        }
        $sentencias = dividirSql($sqlData);
        $n = 0;
        foreach ($sentencias as $s) {
            if (stripos($s, 'SET FOREIGN_KEY_CHECKS') !== false || stripos($s, 'SET NAMES') !== false) {
                continue;
            }
            $pdo->exec($s);
            $n++;
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        $ok("Base de datos importada ({$n} sentencias)");
    } catch (Throwable $e) {
        $fail('Error importando la base de datos: ' . $e->getMessage());
    }

    // 10b) Administrador del nuevo sitio (cambiar correo y/o contraseña).
    if ($adminEmail !== '' || $adminPass !== '') {
        try {
            // ¿Dónde vive el admin? Tabla 'admins' (Cursalia LMS) o 'users'
            // (con role='superadmin' o is_admin=1, según la versión del sitio).
            $tablas = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('admins', $tablas, true)) {
                $tabla = 'admins';
                $where = '1=1';
                $promote = '';
            } else {
                $tabla = 'users';
                $cols = $pdo->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
                $where = in_array('role', $cols, true) ? "role = 'superadmin'" : 'is_admin = 1';
                $promote = $where;
            }

            $row = $pdo->query("SELECT id FROM `{$tabla}` WHERE {$where} ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
            if (! $row && $promote !== '') {
                // No había admin marcado: asciende al primero.
                $row = $pdo->query("SELECT id FROM `{$tabla}` ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $pdo->exec("UPDATE `{$tabla}` SET {$promote} WHERE id = " . (int) $row['id']);
                }
            }
            if ($row) {
                $sets = [];
                $params = [];
                if ($adminEmail !== '') {
                    $sets[] = 'email = ?';
                    $params[] = $adminEmail;
                }
                if ($adminPass !== '') {
                    $sets[] = 'password = ?';
                    $params[] = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);
                }
                $params[] = (int) $row['id'];
                $st = $pdo->prepare("UPDATE `{$tabla}` SET " . implode(', ', $sets) . ' WHERE id = ?');
                $st->execute($params);
                $ok('Administrador del nuevo sitio actualizado' . ($adminEmail !== '' ? ' (' . $adminEmail . ')' : ''));
            } else {
                $log[] = ['r', '⚠ No había administradores para actualizar (se omitió).'];
            }
        } catch (Throwable $e) {
            // No rompemos la instalación: la BD ya está importada.
            $log[] = ['r', '⚠ No se pudo cambiar el administrador (' . $e->getMessage() . '). Usa el del sitio original.'];
        }
    }

    // 11) Limpiar cachés de configuración del código clonado
    foreach (glob($codeDir . '/bootstrap/cache/*.php') ?: [] as $c) {
        @unlink($c);
    }
    $ok('Cachés de configuración limpiadas');

    // Fin
    echo '<h2>🎉 ¡Instalación completada!</h2>';
    echo '<div class="log">';
    foreach ($log as [$cls, $t]) {
        echo '<span class="' . $cls . '">' . h($t) . "</span>\n";
    }
    echo '</div>';
    echo '<div class="okbox">Tu sitio ya debería verse en <strong>' . h($appUrl) . '</strong></div>';
    echo '<div class="note"><strong>Importante — por seguridad, borra ahora:</strong><br>'
        . '• este archivo <code>instalador.php</code><br>'
        . '• el paquete <code>' . h(basename($paquete)) . '</code></div>';
    echo '<a class="btn brand" href="' . h($appUrl) . '" target="_blank">Abrir mi sitio →</a>';
    pie();
    exit;
}

function htaccessLaravel(): string
{
    return <<<'HT'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} -d [OR]
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HT;
}

/* ------------------------------------------------------------------ *
 *  PASO 2: base de datos
 * ------------------------------------------------------------------ */

if ($accion === 'paso2') {
    cabecera('Paso 2 de 4 · Base de datos');
    progreso(2);
    $val = fn ($k, $d = '') => h($_POST[$k] ?? $d);
    $urlGuess = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'midominio.com');
    ?>
    <h2>Datos de la base de datos del nuevo hosting</h2>
    <p class="hint">Los tienes en tu panel (cPanel → Bases de datos MySQL).</p>
    <form method="post">
      <input type="hidden" name="accion" value="paso3">

      <label>Servidor de la base de datos</label>
      <input type="text" name="db_host" value="<?= $val('db_host', 'localhost') ?>" required>
      <p class="hint">Casi siempre es <code>localhost</code>.</p>

      <div class="row">
        <div>
          <label>Nombre de la base de datos</label>
          <input type="text" name="db_name" value="<?= $val('db_name') ?>" placeholder="usuario_cursalia" required>
        </div>
        <div>
          <label>Puerto</label>
          <input type="text" name="db_port" value="<?= $val('db_port', '3306') ?>">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Usuario de la base de datos</label>
          <input type="text" name="db_user" value="<?= $val('db_user') ?>" required>
        </div>
        <div>
          <label>Contraseña</label>
          <input type="password" name="db_pass" value="<?= $val('db_pass') ?>">
        </div>
      </div>

      <label>Dirección del nuevo sitio (dominio)</label>
      <input type="text" name="app_url" value="<?= $val('app_url', $urlGuess) ?>" required>
      <p class="hint">Con https:// y sin barra al final. Ej: <code>https://midominio.com</code></p>

      <p style="margin-top:18px"><button type="submit" class="btn brand">Continuar →</button></p>
    </form>
    <?php
    pie();
    exit;
}

/* ------------------------------------------------------------------ *
 *  PASO 3: carpeta del código + administrador
 * ------------------------------------------------------------------ */

if ($accion === 'paso3') {
    cabecera('Paso 3 de 4 · Sitio y administrador');
    progreso(3);
    $appUrl = trim($_POST['app_url'] ?? '');
    $hostP = preg_replace('/^www\./', '', explode(':', explode('/', preg_replace('#^https?://#', '', $appUrl))[0])[0]);
    $slug = preg_replace('/[^a-z0-9_-]/', '', strtolower(explode('.', $hostP)[0])) ?: 'cursalia';
    $codeDefault = homeDir($webRoot) . '/' . $slug . '_app';
    $arrastre = ['db_host', 'db_port', 'db_name', 'db_user', 'db_pass', 'app_url'];
    ?>
    <h2>Carpeta del código y administrador</h2>
    <form method="post">
      <input type="hidden" name="accion" value="instalar">
      <?php foreach ($arrastre as $k): ?>
        <input type="hidden" name="<?= h($k) ?>" value="<?= h($_POST[$k] ?? '') ?>">
      <?php endforeach; ?>

      <label>Carpeta del código (fuera de la web)</label>
      <input type="text" name="code_dir" value="<?= h($codeDefault) ?>" required>
      <p class="hint">Aquí va el código (con el <code>.env</code>), FUERA de la carpeta pública. Se rellenó solo según tu dominio. ⚠️ Usa una carpeta NUEVA o vacía.</p>

      <div style="border-top:1px solid #eef2f1; margin-top:18px; padding-top:8px;">
        <label style="font-size:15px;">👤 Administrador del nuevo sitio <span style="font-weight:400;color:#64748b;">(opcional)</span></label>
        <div class="row">
          <div>
            <label>Correo del administrador</label>
            <input type="text" name="admin_email" placeholder="admin@<?= h($hostP) ?>">
          </div>
          <div>
            <label>Contraseña nueva</label>
            <input type="password" name="admin_pass" placeholder="••••••••">
          </div>
        </div>
        <p class="hint">Con esto entrarás al panel del NUEVO sitio. Si los dejas vacíos, se mantiene el administrador del sitio original.</p>
      </div>

      <label class="check"><input type="checkbox" name="vaciar" value="1"> Vaciar la base de datos antes de importar (si ya tenía tablas)</label>

      <div class="note">Asegúrate de haber subido el paquete <code>migrador-paquete-*.zip</code> junto a este instalador.</div>

      <p style="margin-top:18px"><button type="submit" class="btn brand">Instalar Cursalia ahora →</button></p>
    </form>

    <form method="post" style="margin-top:10px;">
      <input type="hidden" name="accion" value="paso2">
      <?php foreach ($arrastre as $k): ?>
        <input type="hidden" name="<?= h($k) ?>" value="<?= h($_POST[$k] ?? '') ?>">
      <?php endforeach; ?>
      <button type="submit" class="btn" style="background:#e2e8f0;color:#475569;">← Atrás</button>
    </form>
    <?php
    pie();
    exit;
}

/* ------------------------------------------------------------------ *
 *  PASO: inicio (requisitos)
 * ------------------------------------------------------------------ */

cabecera('Paso 1 de 4 · Requisitos');
$checks = requisitos($webRoot);
$paquete = encontrarPaquete($webRoot);
$bloqueado = false;
$hayAvisos = false;
foreach ($checks as $c) {
    if ($c['level'] === 'bad') {
        $bloqueado = true;
    }
    if ($c['level'] === 'warn') {
        $hayAvisos = true;
    }
}
$todoOk = ! $bloqueado && $paquete !== null;
progreso(1);
?>
<h2>Voy a instalar tu sitio Cursalia aquí</h2>
<p class="hint">Primero reviso que el hosting cumpla lo necesario.</p>
<ul class="checks">
  <?php foreach ($checks as $c):
      $pill = $c['level'] === 'ok' ? 'ok' : ($c['level'] === 'bad' ? 'no' : 'warn');
      $sym = $c['level'] === 'ok' ? '✓' : ($c['level'] === 'bad' ? '✗' : '!');
  ?>
    <li><span class="pill <?= $pill ?>"><?= $sym ?></span><?= h($c['txt']) ?></li>
  <?php endforeach; ?>
  <li>
    <span class="pill <?= $paquete ? 'ok' : 'no' ?>"><?= $paquete ? '✓' : '✗' ?></span>
    <?= $paquete ? 'Paquete encontrado: ' . h(basename($paquete)) : 'Falta el paquete migrador-paquete-*.zip (súbelo aquí)' ?>
  </li>
</ul>
<?php if ($todoOk): ?>
  <?php if ($hayAvisos): ?>
    <div class="note">Los puntos en <strong>amarillo</strong> son avisos: puedes continuar, pero tenlos en cuenta (sobre todo si la web no carga bien tras instalar).</div>
  <?php endif; ?>
  <a class="btn brand" href="?accion=paso2">Continuar →</a>
<?php else: ?>
  <div class="err">Resuelve los puntos en <strong>rojo</strong> y recarga esta página. (Los amarillos son solo avisos.)</div>
<?php endif; ?>
<?php
pie();
