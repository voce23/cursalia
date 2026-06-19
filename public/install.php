<?php

use App\Models\Admin;
use App\Models\GeneralSetting;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

/**
 * ============================================================================
 *  Instalador web de Cursalia LMS  —  estilo WordPress
 * ============================================================================
 *  PHP puro e INDEPENDIENTE del framework. Funciona aunque todavía no exista
 *  .env ni APP_KEY (por eso no arranca el kernel HTTP de Laravel hasta el
 *  último paso, cuando el .env ya está escrito).
 *
 *  Soporta DOS montajes, detectados automáticamente:
 *   • ESTÁNDAR  — el document root del dominio apunta a /public (subdominio).
 *                 La app vive en una sola carpeta; sólo /public es público.
 *   • 2 CARPETAS — el paquete se sube a public_html (dominio principal). El
 *                 instalador MUEVE la app a una carpeta hermana (midominio_app),
 *                 fuera de la web, y deja en public_html sólo lo público.
 *
 *  Seguridad: al terminar crea storage/installed y se autoelimina. Si ya hay
 *  un .env con APP_KEY o el lock, el instalador se niega a re-ejecutarse.
 * ============================================================================
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
@ini_set('display_errors', '0'); // no filtrar errores crudos al navegador; el instalador muestra mensajes controlados
@set_time_limit(300);            // las migraciones pueden tardar en hosting compartido
@ini_set('memory_limit', '256M'); // 82 migraciones + seeders pueden superar 128M

define('CURSALIA_BASE', dirname(__DIR__));
define('CURSALIA_ENV', CURSALIA_BASE.'/.env');
define('CURSALIA_ENV_EXAMPLE', CURSALIA_BASE.'/.env.example');
define('CURSALIA_LOCK', CURSALIA_BASE.'/storage/installed');
define('CURSALIA_DEFAULT_ADMIN_EMAIL', 'admin@lmsl13.test');
define('CURSALIA_VERSION', '2.01');

// ¿Montaje de 2 carpetas? Lo es si el DOCUMENT_ROOT del servidor ES la propia
// carpeta de la app (public_html): el código quedaría expuesto a la web si no
// lo separamos. Si el document root es .../public, es el montaje estándar.
$cursaliaDocRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
define('CURSALIA_SPLIT', $cursaliaDocRoot !== false && $cursaliaDocRoot === realpath(CURSALIA_BASE));

// ───────────────────────────────────────────────────────────────────────────
//  Guard: ¿ya instalado?
// ───────────────────────────────────────────────────────────────────────────
$alreadyInstalled = is_file(CURSALIA_LOCK)
    || (is_file(CURSALIA_ENV) && preg_match('/^APP_KEY=base64:.+/m', (string) @file_get_contents(CURSALIA_ENV)));

if ($alreadyInstalled) {
    layout('Cursalia ya está instalado', wizard_shell(0,
        '<div class="big ok">'.icon_check_lg().'</div>'
        .'<h1 class="center">Cursalia ya está instalado</h1>'
        .'<p class="sub center">Tu plataforma ya está configurada. Por seguridad, el instalador está bloqueado.</p>'
        .'<a class="btn" href="'.e(base_url()).'/admin/login">Ir al panel de administración '.icon_arrow().'</a>'
        .'<p class="fine">¿Reinstalar desde cero? Borra el archivo <code>storage/installed</code> de tu hosting y vuelve a abrir esta página. (Atención: reinstalar puede sobrescribir tus datos.)</p>'
    ));
    exit;
}

// ───────────────────────────────────────────────────────────────────────────
//  Router por pasos
// ───────────────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST' && (($_POST['action'] ?? '') === 'install')) {
    handle_install();
    exit;
}

$step = $_GET['step'] ?? '1';
$requirements = check_requirements();

if ($step === '2') {
    render_form();
} else {
    render_requirements($requirements);
}
exit;

// ═══════════════════════════════════════════════════════════════════════════
//  PASO 1 — Requisitos
// ═══════════════════════════════════════════════════════════════════════════
function check_requirements(): array
{
    $php = PHP_VERSION;
    $exts = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'ctype', 'json', 'curl', 'fileinfo', 'bcmath', 'xml'];

    $checks = [];

    $checks[] = [
        'label' => 'PHP 8.3 o superior',
        'detail' => $php,
        'ok' => version_compare($php, '8.3.0', '>='),
        'critical' => true,
    ];

    $missing = array_values(array_filter($exts, fn ($x) => ! extension_loaded($x)));
    $checks[] = [
        'label' => 'Extensiones de PHP',
        'detail' => $missing ? 'Faltan: '.implode(', ', $missing) : 'pdo_mysql, mbstring, openssl…',
        'ok' => count($missing) === 0,
        'critical' => true,
    ];

    // Integridad de vendor/: no basta con autoload.php — algunos extractores
    // (File Manager de cPanel) dejan archivos sin extraer y el fallo aparece
    // luego, a media instalación, con un error críptico. Verificamos varios
    // archivos críticos repartidos por el árbol para detectar una extracción
    // incompleta AQUÍ, antes de empezar.
    $vendorCritical = [
        '/vendor/autoload.php',
        '/vendor/composer/platform_check.php',
        '/vendor/composer/autoload_real.php',
        '/vendor/symfony/deprecation-contracts/function.php',
        '/bootstrap/app.php',
    ];
    $vendorMissing = array_values(array_filter(
        $vendorCritical,
        fn ($f) => ! is_file(CURSALIA_BASE.$f)
    ));
    $checks[] = [
        'label' => 'Dependencias completas (vendor/)',
        'detail' => $vendorMissing
            ? 'Extracción INCOMPLETA (faltan '.implode(', ', array_map('basename', $vendorMissing))
                .'). Re-extrae el paquete; lo más fiable es «unzip» por Terminal.'
            : 'vendor/ completa y verificada',
        'ok' => count($vendorMissing) === 0,
        'critical' => true,
    ];

    $envWritable = is_writable(CURSALIA_BASE) || (is_file(CURSALIA_ENV) && is_writable(CURSALIA_ENV));
    $checks[] = [
        'label' => 'Permiso para crear el .env',
        'detail' => $envWritable ? 'Carpeta raíz escribible' : 'Sin permiso de escritura en la carpeta raíz',
        'ok' => $envWritable,
        'critical' => true,
    ];

    // Si storage/ no es escribible, intentamos auto-corregir el permiso (una
    // extracción defectuosa la deja a veces en 0644, sin bit de ejecución).
    $storageDir = CURSALIA_BASE.'/storage';
    if (! is_writable($storageDir)) {
        @chmod($storageDir, 0755);
    }
    $storageWritable = is_writable($storageDir);
    $checks[] = [
        'label' => 'Carpeta storage/ escribible',
        'detail' => $storageWritable ? 'Correcto' : 'Da permisos 755 a la carpeta storage/',
        'ok' => $storageWritable,
        'critical' => true,
    ];

    // En el montaje de 2 carpetas, comprobamos que se pueda crear la carpeta hermana.
    if (CURSALIA_SPLIT) {
        $home = dirname(CURSALIA_BASE);
        $checks[] = [
            'label' => 'Montaje seguro de 2 carpetas',
            'detail' => is_writable($home)
                ? 'Se podrá separar la app fuera de la web'
                : 'No se puede escribir fuera de public_html (usa un subdominio con /public)',
            'ok' => is_writable($home),
            'critical' => true,
        ];
    }

    return $checks;
}

function requirements_ok(array $checks): bool
{
    foreach ($checks as $c) {
        if ($c['critical'] && ! $c['ok']) {
            return false;
        }
    }

    return true;
}

function render_requirements(array $checks): void
{
    $allOk = requirements_ok($checks);

    $rows = '';
    foreach ($checks as $c) {
        $rows .= '<div class="chk">'
            .status_dot($c['ok'])
            .'<span class="chk-l">'.e($c['label']).'</span>'
            .'<span class="chk-d">'.e($c['detail']).'</span>'
            .'</div>';
    }

    if ($allOk) {
        $note = '<div class="note ok">'.icon_check().'<span>Tu hosting cumple todo. ¡Listo para continuar!</span></div>';
        $cta = '<a class="btn" href="?step=2">Continuar con la instalación '.icon_arrow().'</a>';
    } else {
        $note = '<div class="note bad"><span>Hay requisitos sin cumplir (en rojo). Corrígelos y recarga esta página. Si tienes dudas, tu hosting puede ayudarte a activarlos.</span></div>';
        $cta = '<a class="btn ghost" href="?step=1">Volver a comprobar</a>';
    }

    layout('Instalar Cursalia · Requisitos', wizard_shell(1,
        '<h1>Vamos a instalar Cursalia</h1>'
        .'<p class="sub">Comprobamos que tu hosting cumple lo necesario. Tarda unos segundos.</p>'
        .'<div class="checks">'.$rows.'</div>'
        .$note
        .$cta
    ));
}

// ═══════════════════════════════════════════════════════════════════════════
//  PASO 2 — Formulario
// ═══════════════════════════════════════════════════════════════════════════
function render_form(array $old = [], array $errors = []): void
{
    // Re-chequeo defensivo: no permitir el form si faltan requisitos críticos.
    if (! requirements_ok(check_requirements())) {
        header('Location: ?step=1');
        exit;
    }

    $v = function (string $k, string $def = '') use ($old) {
        return e($old[$k] ?? $def);
    };

    $errBox = '';
    if ($errors) {
        $errBox = '<div class="note bad"><span><strong>No se pudo continuar:</strong> '.e(implode(' ', $errors)).'</span></div>';
    }

    // En montaje de 2 carpetas, mostramos el nombre de la carpeta de la app.
    $splitBox = '';
    if (CURSALIA_SPLIT) {
        $splitBox = '<div class="note info"><span>'.icon_shield()
            .' <strong>Montaje seguro detectado.</strong> Instalaremos el código de Cursalia en una carpeta aparte (fuera de la web) para máxima seguridad. Tú solo confirma el nombre.</span></div>'
            .'<label>Carpeta de la aplicación
                <input name="app_folder" value="'.$v('app_folder', default_app_folder()).'" placeholder="midominio_app" required>
                <span class="hint">Se creará junto a <code>public_html</code>. Déjalo así si no estás seguro.</span>
            </label>';
    }

    $form = '<form method="post" action="" autocomplete="off">
            <input type="hidden" name="action" value="install">
            <h1>Configura tu plataforma</h1>
            <p class="sub">Estos datos los crea tu hosting (cPanel). Si no tienes la base de datos, créala primero en <em>cPanel → Bases de datos MySQL</em>.</p>

            '.$errBox.$splitBox.'

            <div class="sec">'.icon_db().' Base de datos</div>
            <div class="grid">
                <label>Servidor (host)
                    <input name="db_host" value="'.$v('db_host', 'localhost').'" placeholder="localhost" required>
                    <span class="hint">Casi siempre <code>localhost</code>.</span>
                </label>
                <label>Puerto
                    <input name="db_port" value="'.$v('db_port', '3306').'" placeholder="3306" required>
                </label>
            </div>
            <label>Nombre de la base de datos
                <input name="db_database" value="'.$v('db_database').'" placeholder="usuario_cursalia" required>
            </label>
            <div class="grid">
                <label>Usuario de la base de datos
                    <input name="db_username" value="'.$v('db_username').'" placeholder="usuario_cursalia" required>
                </label>
                <label>Contraseña de la base de datos
                    <input type="password" name="db_password" value="'.$v('db_password').'" placeholder="••••••••">
                </label>
            </div>

            <div class="sec">'.icon_globe().' Tu sitio</div>
            <div class="grid">
                <label>Nombre del sitio
                    <input name="site_name" value="'.$v('site_name', 'Cursalia').'" placeholder="Mi Academia" required>
                </label>
                <label>Dirección web (URL)
                    <input name="app_url" value="'.$v('app_url', detected_url()).'" placeholder="https://midominio.com" required>
                </label>
            </div>

            <div class="sec">'.icon_user().' Administrador</div>
            <p class="fine" style="margin-top:-4px">Con esta cuenta entrarás al panel para crear cursos y publicar.</p>
            <label>Nombre del administrador
                <input name="admin_name" value="'.$v('admin_name').'" placeholder="Tu nombre" required>
            </label>
            <div class="grid">
                <label>Email del administrador
                    <input type="email" name="admin_email" value="'.$v('admin_email').'" placeholder="tu@correo.com" required>
                </label>
                <label>Contraseña (mín. 8)
                    <input type="password" name="admin_password" placeholder="••••••••" minlength="8" required>
                </label>
            </div>

            <div class="sec optional">'.icon_mail().' Correo <span>(opcional)</span></div>
            <p class="fine" style="margin-top:-4px">Para la recuperación de contraseña. Puedes dejarlo y configurarlo más tarde. Crea antes la cuenta de correo en cPanel.</p>
            <div class="grid">
                <label>Servidor SMTP
                    <input name="mail_host" value="'.$v('mail_host').'" placeholder="mail.midominio.com">
                </label>
                <label>Puerto
                    <input name="mail_port" value="'.$v('mail_port', '465').'" placeholder="465">
                </label>
            </div>
            <div class="grid">
                <label>Usuario (email)
                    <input name="mail_username" value="'.$v('mail_username').'" placeholder="hola@midominio.com">
                </label>
                <label>Contraseña del correo
                    <input type="password" name="mail_password" value="'.$v('mail_password').'" placeholder="••••••••">
                </label>
            </div>

            <button class="btn" type="submit">Instalar Cursalia ahora '.icon_arrow().'</button>
            <p class="fine center" style="margin-top:14px">El proceso tarda hasta un minuto. No cierres esta ventana.</p>
        </form>';

    layout('Instalar Cursalia · Configuración', wizard_shell(2, $form));
}

// ═══════════════════════════════════════════════════════════════════════════
//  PASO 3 — Proceso de instalación
// ═══════════════════════════════════════════════════════════════════════════
function handle_install(): void
{
    $in = function (string $k): string {
        return trim((string) ($_POST[$k] ?? ''));
    };

    $data = [
        'db_host' => $in('db_host'),
        'db_port' => $in('db_port') ?: '3306',
        'db_database' => $in('db_database'),
        'db_username' => $in('db_username'),
        'db_password' => $in('db_password'),
        'site_name' => $in('site_name') ?: 'Cursalia',
        'app_url' => rtrim($in('app_url'), '/'),
        'admin_name' => $in('admin_name'),
        'admin_email' => $in('admin_email'),
        'admin_password' => (string) ($_POST['admin_password'] ?? ''),
        'mail_host' => $in('mail_host'),
        'mail_port' => $in('mail_port') ?: '465',
        'mail_username' => $in('mail_username'),
        'mail_password' => $in('mail_password'),
        'app_folder' => CURSALIA_SPLIT ? sanitize_app_folder($in('app_folder')) : '',
    ];

    // ── Validación ──────────────────────────────────────────────────────────
    $errors = [];
    foreach (['db_host', 'db_database', 'db_username', 'site_name', 'app_url', 'admin_name', 'admin_email'] as $req) {
        if ($data[$req] === '') {
            $errors[] = 'Falta un campo obligatorio.';
            break;
        }
    }
    if (! filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email del administrador no es válido.';
    }
    if (strlen($data['admin_password']) < 8) {
        $errors[] = 'La contraseña del administrador debe tener al menos 8 caracteres.';
    }
    if (! filter_var($data['app_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'La dirección web (URL) no es válida. Ejemplo: https://midominio.com';
    }
    if ($errors) {
        render_form($data, $errors);

        return;
    }

    // ── 1) Probar conexión a la base de datos ────────────────────────────────
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $data['db_host'], $data['db_port'], $data['db_database']);
        $pdo = new PDO($dsn, $data['db_username'], $data['db_password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 6,
        ]);
        unset($pdo);
    } catch (PDOException $e) {
        $hint = (str_contains($e->getMessage(), '1045') || str_contains($e->getMessage(), 'denied'))
            ? 'El usuario o la contraseña de la base de datos no son correctos.'
            : (str_contains($e->getMessage(), '1049')
                ? 'La base de datos indicada no existe. Créala en cPanel → Bases de datos MySQL.'
                : 'No se pudo conectar. Revisa el servidor, el nombre de la base de datos, el usuario y la contraseña en cPanel.');
        render_form($data, [$hint]);

        return;
    }

    // ── 2) Montaje de 2 carpetas (si aplica): mover la app FUERA de la web ────
    //     Se hace ANTES de escribir el .env. Con pre-chequeos: si algo no se
    //     puede, abortamos sin haber movido nada (el sitio queda intacto).
    $appBase = CURSALIA_BASE;
    if (CURSALIA_SPLIT) {
        try {
            $appBase = cursalia_prepare_split($data['app_folder']);
        } catch (Throwable $e) {
            render_form($data, [
                'No se pudo preparar el montaje seguro de 2 carpetas: '.$e->getMessage()
                .' Como alternativa, instala en un subdominio con el document root apuntando a /public.',
            ]);

            return;
        }
    }

    // ── 2.5) Reparar permisos de una extracción defectuosa ───────────────────
    //     Algunos extractores (File Manager de cPanel) dejan carpetas en 0644:
    //     sin bit de ejecución NO se pueden recorrer → 500 y errores raros.
    //     Ponemos TODOS los directorios en 0755 (rápido: solo carpetas).
    cursalia_fix_permissions($appBase);

    // ── 3) Escribir el .env (con APP_KEY único) en la carpeta de la app ──────
    $envPath = $appBase.'/.env';
    $examplePath = $appBase.'/.env.example';
    $template = is_file($examplePath) ? file_get_contents($examplePath) : default_env_template();

    $appKey = 'base64:'.base64_encode(random_bytes(32));
    $secureCookie = str_starts_with($data['app_url'], 'https://') ? 'true' : 'false';

    $repl = [
        'APP_NAME' => $data['site_name'],
        'APP_ENV' => 'production',
        'APP_KEY' => $appKey,
        'APP_DEBUG' => 'false',
        'APP_URL' => $data['app_url'],
        'LOG_LEVEL' => 'error',
        'DB_CONNECTION' => 'mysql',
        'DB_HOST' => $data['db_host'],
        'DB_PORT' => $data['db_port'],
        'DB_DATABASE' => $data['db_database'],
        'DB_USERNAME' => $data['db_username'],
        'DB_PASSWORD' => $data['db_password'],
        'CACHE_STORE' => 'file',
        'SESSION_DRIVER' => 'file',
        'SESSION_SECURE_COOKIE' => $secureCookie,
        'QUEUE_CONNECTION' => 'sync',
    ];

    if ($data['mail_host'] !== '' && $data['mail_username'] !== '') {
        $repl['MAIL_MAILER'] = 'smtp';
        $repl['MAIL_HOST'] = $data['mail_host'];
        $repl['MAIL_PORT'] = $data['mail_port'];
        $repl['MAIL_USERNAME'] = $data['mail_username'];
        $repl['MAIL_PASSWORD'] = $data['mail_password'];
        // El LMS usa MAIL_SCHEME (no MAIL_ENCRYPTION): smtps para 465, si no STARTTLS.
        $repl['MAIL_SCHEME'] = ($data['mail_port'] === '465') ? 'smtps' : 'smtp';
        $repl['MAIL_FROM_ADDRESS'] = $data['mail_username'];
    }

    $env = $template;
    foreach ($repl as $key => $value) {
        $env = env_set($env, $key, $value);
    }

    if (@file_put_contents($envPath, $env) === false) {
        render_install_error('No se pudo escribir el archivo .env en la carpeta de la app.');

        return;
    }

    // ── 4) Arrancar Laravel y migrar + sembrar ───────────────────────────────
    try {
        require_once $appBase.'/vendor/autoload.php';

        /** @var Application $app */
        $app = require $appBase.'/bootstrap/app.php';

        // En 2 carpetas, la web es public_html aunque la app viva aparte.
        if (CURSALIA_SPLIT) {
            $app->usePublicPath(CURSALIA_BASE);
        }

        /** @var Kernel $kernel */
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();

        $log = '';

        $migrate = $kernel->call('migrate', ['--force' => true]);
        $log .= $kernel->output();
        if ($migrate !== 0) {
            throw new RuntimeException("Fallaron las migraciones:\n".$log);
        }

        $seed = $kernel->call('db:seed', ['--force' => true]);
        $log .= $kernel->output();
        if ($seed !== 0) {
            throw new RuntimeException("Falló la siembra de datos:\n".$log);
        }

        // ── Crear/ajustar el administrador con TUS credenciales ───────────────
        $admin = Admin::query()->firstWhere('email', CURSALIA_DEFAULT_ADMIN_EMAIL)
            ?? Admin::query()->first()
            ?? new Admin;

        $admin->name = $data['admin_name'];
        $admin->email = $data['admin_email'];
        $admin->password = $data['admin_password']; // el cast 'hashed' lo cifra al guardar
        $admin->save();

        Admin::query()
            ->where('email', CURSALIA_DEFAULT_ADMIN_EMAIL)
            ->where('id', '!=', $admin->id)
            ->delete();

        // Reflejar el nombre del sitio elegido como marca visible.
        try {
            GeneralSetting::query()->where('id', 1)->update(['site_name' => $data['site_name']]);
        } catch (Throwable $e) {
        }

        // ── Enlace de storage hacia la web (con fallback a copia) ─────────────
        try {
            $kernel->call('storage:link');
        } catch (Throwable $e) {
            // continuamos al fallback
        }
        $webStorage = (CURSALIA_SPLIT ? CURSALIA_BASE : CURSALIA_BASE.'/public').'/storage';
        $storageSource = $appBase.'/storage/app/public';
        if (! is_dir($webStorage) && is_dir($storageSource)) {
            cursalia_copy_dir($storageSource, $webStorage);
        }

        // ── Finalizar el montaje de 2 carpetas (mover lo público + index.php) ─
        if (CURSALIA_SPLIT) {
            cursalia_finalize_split($appBase, $data['app_folder']);
        }

        try {
            $kernel->call('config:clear');
            $kernel->call('cache:clear');
        } catch (Throwable $e) {
        }
    } catch (Throwable $e) {
        @unlink($envPath);
        @unlink($appBase.'/storage/installed');
        $note = CURSALIA_SPLIT
            ? "\n\nNota: la app se movió a la carpeta «".$data['app_folder'].'». Si vas a reintentar, borra primero esa carpeta.'
            : '';
        render_install_error($e->getMessage().$note);

        return;
    }

    // ── 5) Bloquear el instalador y autoeliminarlo ───────────────────────────
    @file_put_contents($appBase.'/storage/installed', 'Cursalia instalado el '.date('c')."\n");
    @unlink(__FILE__);

    render_success($data);
}

// ═══════════════════════════════════════════════════════════════════════════
//  Montaje de 2 carpetas — helpers
// ═══════════════════════════════════════════════════════════════════════════

/** Devuelve el nombre por defecto de la carpeta de la app: <dominio>_app. */
function default_app_folder(): string
{
    $host = preg_replace('/^www\./', '', strtolower($_SERVER['HTTP_HOST'] ?? 'cursalia'));
    $label = explode('.', explode(':', $host)[0])[0];   // antes del puerto y del primer punto
    $label = preg_replace('/[^a-z0-9_]/', '', $label);

    return ($label !== '' ? $label : 'cursalia').'_app';
}

function sanitize_app_folder(string $name): string
{
    $name = preg_replace('/[^a-z0-9_\-]/', '', strtolower(trim($name)));

    return $name !== '' ? $name : default_app_folder();
}

/**
 * Pre-chequea y MUEVE la app a la carpeta hermana (fuera de la web).
 * Lanza excepción si algo no es posible ANTES de mover nada destructivo.
 * Devuelve la ruta de la nueva carpeta de la app.
 */
function cursalia_prepare_split(string $folder): string
{
    $home = dirname(CURSALIA_BASE);          // .../  (padre de public_html)
    $newApp = $home.'/'.$folder;

    if (! is_writable($home)) {
        throw new RuntimeException('no se puede escribir fuera de public_html');
    }
    if (is_dir($newApp) && count(@scandir($newApp) ?: []) > 2) {
        throw new RuntimeException('la carpeta «'.$folder.'» ya existe y no está vacía; elige otro nombre');
    }
    if (! is_dir($newApp) && ! @mkdir($newApp, 0755)) {
        throw new RuntimeException('no se pudo crear la carpeta «'.$folder.'»');
    }
    // Prueba de escritura real antes de mover nada.
    $probe = $newApp.'/.write_probe';
    if (@file_put_contents($probe, 'ok') === false) {
        throw new RuntimeException('sin permiso de escritura en «'.$folder.'»');
    }
    @unlink($probe);

    // Mover a la carpeta hermana TODO lo que haya en la raíz MENOS /public y el
    // index.php de arranque (que se queda en public_html y se reescribe luego).
    // Así nada de la app/config queda expuesto en la web.
    $skip = ['.', '..', 'public', 'index.php'];
    foreach (@scandir(CURSALIA_BASE) ?: [] as $it) {
        if (in_array($it, $skip, true)) {
            continue;
        }
        if (! @rename(CURSALIA_BASE.'/'.$it, $newApp.'/'.$it)) {
            throw new RuntimeException('no se pudo mover «'.$it.'»');
        }
    }

    return $newApp;
}

/**
 * Tras instalar: sube el contenido de /public a public_html, escribe el
 * index.php definitivo (que apunta a la carpeta de la app) y limpia restos.
 */
function cursalia_finalize_split(string $newApp, string $appFolder): void
{
    $web = CURSALIA_BASE;          // public_html
    $pub = $web.'/public';

    // Subir los assets públicos (build, .htaccess, favicon, robots…) a la raíz web.
    if (is_dir($pub)) {
        foreach (@scandir($pub) ?: [] as $it) {
            if (in_array($it, ['.', '..', 'index.php', 'install.php'], true)) {
                continue;
            }
            @rename($pub.'/'.$it, $web.'/'.$it);
        }
    }

    // index.php definitivo: arranca Laravel desde la carpeta hermana de la app.
    $idx = "<?php\n\n"
        ."use Illuminate\\Foundation\\Application;\n"
        ."use Illuminate\\Http\\Request;\n\n"
        ."define('LARAVEL_START', microtime(true));\n\n"
        ."\$APP = __DIR__ . '/../".$appFolder."';\n\n"
        ."if (file_exists(\$m = \$APP . '/storage/framework/maintenance.php')) {\n    require \$m;\n}\n\n"
        ."require \$APP . '/vendor/autoload.php';\n\n"
        ."/** @var Application \$app */\n"
        ."\$app = require \$APP . '/bootstrap/app.php';\n"
        ."\$app->usePublicPath(__DIR__);\n\n"
        ."\$app->handleRequest(Request::capture());\n";
    @file_put_contents($web.'/index.php', $idx);

    // Limpiar la carpeta /public sobrante (incluido este instalador).
    @unlink($pub.'/index.php');
    @unlink($pub.'/install.php');
    @rmdir($pub);
}

// ═══════════════════════════════════════════════════════════════════════════
//  Pantallas de resultado
// ═══════════════════════════════════════════════════════════════════════════
function render_success(array $data): void
{
    $login = e(rtrim($data['app_url'], '/').'/admin/login');

    $extra = '';
    if (CURSALIA_SPLIT && ! empty($data['app_folder'])) {
        $extra = '<div class="note ok" style="margin-top:14px"><span>'.icon_shield()
            .' El código de Cursalia se instaló de forma segura en la carpeta <code>'.e($data['app_folder'])
            .'</code>, fuera de la web.</span></div>';
    }

    layout('¡Cursalia instalado!', wizard_shell(3,
        '<div class="big ok">'.icon_check_lg().'</div>'
        .'<h1 class="center">¡Listo! Cursalia está instalado</h1>'
        .'<p class="sub center">Tu plataforma de cursos ya está funcionando en tu dominio.</p>'
        .'<div class="cred">'
        .'<span>Entra al panel con:</span>'
        .'<strong>'.e($data['admin_email']).'</strong>'
        .'<em>la contraseña que acabas de elegir</em>'
        .'</div>'
        .'<a class="btn" href="'.$login.'">Entrar al panel de administración '.icon_arrow().'</a>'
        .$extra
        .'<div class="note info" style="margin-top:14px"><span><strong>¿Sale un error 500 al entrar?</strong> Tu dominio puede estar usando una versión de PHP antigua. En cPanel → <em>MultiPHP Manager</em>, selecciona tu dominio y ponlo en <strong>PHP 8.3</strong>.</span></div>'
        .'<div class="note info" style="margin-top:14px"><span><strong>Consejo:</strong> si el archivo <code>install.php</code> sigue en tu hosting, bórralo. El instalador ya está bloqueado, pero eliminarlo es lo más limpio.</span></div>'
    ));
}

function render_install_error(string $message): void
{
    layout('Error durante la instalación', wizard_shell(0,
        '<div class="big bad">'.icon_cross_lg().'</div>'
        .'<h1 class="center">Algo falló durante la instalación</h1>'
        .'<p class="sub center">No te preocupes. Revisa el detalle, corrige y vuelve a intentarlo.</p>'
        .'<pre class="log">'.e($message).'</pre>'
        .'<a class="btn ghost" href="?step=2">← Volver al formulario</a>'
        .'<p class="fine">Si menciona la <strong>base de datos</strong>, revisa sus datos en cPanel. '
        .'Si menciona <strong>permisos</strong>, da 755 a <code>storage</code> y <code>bootstrap/cache</code>. '
        .'Si menciona <strong>«platform»</strong> o una versión de PHP, pon tu dominio en <strong>PHP 8.3</strong> (cPanel → MultiPHP Manager). '
        .'Si menciona un <strong>archivo que falta</strong> en <code>vendor/</code>, la extracción quedó incompleta: re-extrae el paquete con <code>unzip</code> por Terminal.</p>'
    ));
}

// ═══════════════════════════════════════════════════════════════════════════
//  Utilidades
// ═══════════════════════════════════════════════════════════════════════════
function env_set(string $content, string $key, string $value): string
{
    // Reglas de entrecomillado seguras para phpdotenv:
    //  - Valor "simple" (sin espacios ni símbolos): sin comillas.
    //  - Si NO contiene comilla simple: comillas SIMPLES → literal, sin
    //    interpolación de ${VAR} (protege contraseñas con $, # o *).
    //  - Si contiene comilla simple: comillas dobles, escapando \ y ".
    if ($value !== '' && ! preg_match('/[\s#"\'\\\\$]/', $value)) {
        $quoted = $value;
    } elseif (! str_contains($value, "'")) {
        $quoted = "'".$value."'";
    } else {
        $quoted = '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
    }

    $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
    if (preg_match($pattern, $content)) {
        return preg_replace($pattern, $key.'='.$quoted, $content, 1);
    }

    return rtrim($content)."\n".$key.'='.$quoted."\n";
}

/**
 * Repara permisos tras una extracción defectuosa: pone TODOS los directorios
 * del árbol en 0755 (transitables). Un extractor que los deja en 0644 (sin bit
 * de ejecución) impide recorrerlos → la app da 500 o errores de "Permission
 * denied". Solo toca carpetas (rápido), no los miles de archivos de vendor/.
 */
function cursalia_fix_permissions(string $base): void
{
    @chmod($base, 0755);
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST   // la carpeta ANTES que su contenido
        );
        foreach ($it as $item) {
            if ($item->isDir()) {
                @chmod($item->getPathname(), 0755);
            }
        }
    } catch (Throwable $e) {
        // best-effort: si algo no se puede, seguimos.
    }
}

/** Copia recursiva de un directorio (fallback cuando storage:link no está permitido). */
function cursalia_copy_dir(string $src, string $dst): void
{
    @mkdir($dst, 0755, true);
    $items = @scandir($src) ?: [];
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $from = $src.'/'.$item;
        $to = $dst.'/'.$item;
        if (is_dir($from)) {
            cursalia_copy_dir($from, $to);
        } else {
            @copy($from, $to);
        }
    }
}

function default_env_template(): string
{
    return "APP_NAME=Cursalia\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\n"
        ."APP_LOCALE=es\nAPP_FALLBACK_LOCALE=es\n\nLOG_CHANNEL=stack\nLOG_LEVEL=error\n\n"
        ."DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=cursalia\nDB_USERNAME=root\nDB_PASSWORD=\n\n"
        ."SESSION_DRIVER=file\nSESSION_LIFETIME=120\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n\n"
        ."MAIL_MAILER=log\nMAIL_FROM_ADDRESS=\"hola@cursalia.test\"\nMAIL_FROM_NAME=\"\${APP_NAME}\"\n\n"
        ."CURSALIA_PAYMENTS_ENABLED=false\n";
}

function detected_url(): string
{
    return detected_scheme().'://'.($_SERVER['HTTP_HOST'] ?? 'midominio.com');
}

function detected_scheme(): string
{
    $https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    return $https ? 'https' : 'http';
}

function base_url(): string
{
    return detected_scheme().'://'.($_SERVER['HTTP_HOST'] ?? '');
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ═══════════════════════════════════════════════════════════════════════════
//  Componentes visuales (cabecera, pasos, iconos SVG en línea)
// ═══════════════════════════════════════════════════════════════════════════
function wizard_shell(int $step, string $inner): string
{
    $progress = '';
    if ($step > 0) {
        $labels = [1 => 'Requisitos', 2 => 'Configuración', 3 => '¡Listo!'];
        $bars = '';
        $tabs = '';
        foreach ($labels as $i => $lbl) {
            $bars .= '<div class="bar'.($i <= $step ? ' on' : '').'"></div>';
            $cls = $i === $step ? 'now' : ($i < $step ? 'done' : '');
            $tabs .= '<span class="'.$cls.'">'.$i.' · '.e($lbl).'</span>';
        }
        $progress = '<div class="bars">'.$bars.'</div><div class="tabs">'.$tabs.'</div>';
    }

    return '<div class="card">'
        .'<div class="hd">'.logo_mark()
        .'<div><div class="hd-t">Cursalia</div><div class="hd-s">Asistente de instalación · v'.CURSALIA_VERSION.'</div></div>'
        .'</div>'
        .$progress
        .'<div class="bd">'.$inner.'</div>'
        .'</div>';
}

function logo_mark(): string
{
    return '<span class="logo-sq"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 12l9 5 9-5"/><path d="M3 16.5l9 5 9-5"/></svg></span>';
}

function status_dot(bool $ok): string
{
    return '<span class="dot '.($ok ? 'ok' : 'bad').'">'.($ok ? icon_check() : icon_cross()).'</span>';
}

function icon_check(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';
}

function icon_cross(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
}

function icon_check_lg(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';
}

function icon_cross_lg(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>';
}

function icon_arrow(): string
{
    return '<svg class="ar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h13M13 6l6 6-6 6"/></svg>';
}

function icon_shield(): string
{
    return '<svg class="ti" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline;width:15px;height:15px;vertical-align:-2px"><path d="M12 3l8 3v6c0 4.5-3.2 7.6-8 9-4.8-1.4-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg>';
}

function icon_db(): string
{
    return '<svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg>';
}

function icon_globe(): string
{
    return '<svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>';
}

function icon_user(): string
{
    return '<svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>';
}

function icon_mail(): string
{
    return '<svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>';
}

// ═══════════════════════════════════════════════════════════════════════════
//  Plantilla / estilos
// ═══════════════════════════════════════════════════════════════════════════
function layout(string $title, string $body): void
{
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8">'
        .'<meta name="viewport" content="width=device-width, initial-scale=1">'
        .'<meta name="robots" content="noindex,nofollow">'
        .'<title>'.e($title).'</title><style>'.styles().'</style></head>'
        .'<body><main>'.$body.'</main></body></html>';
}

function styles(): string
{
    return <<<'CSS'
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--green:#16a34a;--green-d:#15803d;--green-t:#dcfce7;--dark:#0f172a;
              --ink:#0f172a;--muted:#64748b;--faint:#94a3b8;--line:#e2e8f0;
              --line-2:#f1f5f9;--bg:#eef2f0;--bad:#dc2626}
        body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;
             background:var(--bg);color:var(--ink);line-height:1.55;
             min-height:100vh;display:flex;align-items:flex-start;justify-content:center;
             padding:32px 16px}
        main{width:100%;max-width:520px}
        .card{background:#fff;border:1px solid var(--line);border-radius:20px;
              overflow:hidden;box-shadow:0 14px 40px -22px rgba(15,23,42,.4)}
        .hd{background:var(--dark);padding:20px 28px;display:flex;align-items:center;gap:12px}
        .logo-sq{width:38px;height:38px;border-radius:11px;background:var(--green);
                 color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .logo-sq svg{width:21px;height:21px}
        .hd-t{font-size:17px;font-weight:600;color:#fff;line-height:1.1}
        .hd-s{font-size:12px;color:var(--faint);margin-top:2px}
        .bars{display:flex;gap:6px;padding:16px 28px 0}
        .bar{flex:1;height:4px;border-radius:99px;background:var(--line)}
        .bar.on{background:var(--green)}
        .tabs{display:flex;justify-content:space-between;padding:8px 28px 0;font-size:11.5px}
        .tabs span{color:var(--faint)}
        .tabs .now{color:var(--green-d);font-weight:600}
        .tabs .done{color:var(--green)}
        .bd{padding:22px 28px 28px}
        h1{font-size:21px;font-weight:700;letter-spacing:-.01em;margin-bottom:6px}
        h1.center,.sub.center,.fine.center{text-align:center}
        .sub{font-size:14px;color:var(--muted);margin-bottom:18px;line-height:1.55}
        .fine{font-size:12.5px;color:var(--faint);line-height:1.5;margin-top:16px}
        code{background:var(--line-2);padding:2px 6px;border-radius:5px;font-size:12.5px;
             font-family:ui-monospace,monospace}
        em{font-style:normal;color:var(--ink)}
        .checks{display:flex;flex-direction:column;margin-bottom:4px}
        .chk{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid var(--line-2)}
        .chk:last-child{border-bottom:0}
        .chk-l{flex:1;font-size:14px;font-weight:600}
        .chk-d{font-size:12px;color:var(--faint);text-align:right;max-width:48%}
        .dot{flex-shrink:0;width:26px;height:26px;border-radius:50%;display:flex;
             align-items:center;justify-content:center}
        .dot svg{width:15px;height:15px}
        .dot.ok{background:var(--green-t);color:var(--green)}
        .dot.bad{background:#fee2e2;color:var(--bad)}
        .note{display:flex;align-items:center;gap:9px;border-radius:12px;
              padding:11px 14px;margin:18px 0;font-size:13px;line-height:1.45}
        .note svg{width:17px;height:17px;flex-shrink:0}
        .note.ok{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green-d)}
        .note.bad{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
        .note.info{background:#f0f9ff;border:1px solid #bae6fd;color:#075985}
        .note strong{font-weight:700}
        .btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;
             background:var(--green);color:#fff;font-weight:600;font-size:15px;padding:13px;
             border:0;border-radius:12px;cursor:pointer;text-decoration:none;
             transition:background .15s}
        .btn:hover{background:var(--green-d)}
        .btn.ghost{background:#fff;color:var(--ink);border:1px solid var(--line)}
        .btn.ghost:hover{background:#f8fafc}
        .btn .ar{width:18px;height:18px}
        .sec{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;
             text-transform:uppercase;letter-spacing:.04em;color:var(--muted);
             margin:24px 0 12px;padding-top:18px;border-top:1px solid var(--line)}
        .sec .si{width:17px;height:17px;color:var(--green)}
        .sec.optional{color:var(--faint)}
        .sec.optional .si{color:var(--faint)}
        .sec span{font-weight:500;text-transform:none;letter-spacing:0}
        label{display:block;font-weight:600;font-size:13.5px;margin-bottom:13px}
        input{width:100%;margin-top:6px;padding:11px 13px;border:1px solid var(--line);
              border-radius:10px;font-size:15px;font-weight:400;background:#fff;color:var(--ink)}
        input:focus{outline:none;border-color:var(--green);box-shadow:0 0 0 3px #16a34a22}
        .hint{display:block;font-weight:400;font-size:12px;color:var(--faint);margin-top:5px}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        @media(max-width:520px){.grid{grid-template-columns:1fr}}
        .big{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;
             justify-content:center;margin:4px auto 18px}
        .big svg{width:32px;height:32px}
        .big.ok{background:var(--green-t);color:var(--green)}
        .big.bad{background:#fee2e2;color:var(--bad)}
        .cred{background:#f8fafc;border:1px solid var(--line);border-radius:12px;
              padding:18px;margin:20px 0;display:flex;flex-direction:column;gap:4px;text-align:center}
        .cred span{font-size:13px;color:var(--muted)}
        .cred strong{font-size:17px;font-weight:700}
        .cred em{font-size:13px;color:var(--muted)}
        .log{background:#0f172a;color:#e2e8f0;padding:16px;border-radius:10px;
             font-family:ui-monospace,monospace;font-size:12px;white-space:pre-wrap;
             word-break:break-word;max-height:280px;overflow:auto;margin:16px 0}
    CSS;
}
