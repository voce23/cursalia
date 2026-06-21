# =============================================================================
#  build-package.ps1  —  Genera el ZIP de descarga de Cursalia LMS
# =============================================================================
#  Crea un paquete "listo para instalar" que tus usuarios suben a su hosting
#  y configuran con el instalador web (public/install.php), SIN terminal.
#
#  El ZIP INCLUYE:  vendor/ y public/build/ ya compilados (no necesitan
#                   composer ni npm), el instalador y .env.example.
#  El ZIP EXCLUYE:  .env, node_modules, public/hot, el symlink public/storage,
#                   tu base de datos local, cachés y archivos internos.
#
#  Uso:   pwsh/powershell  ->  .\build-package.ps1
#  Salida: un .zip en tu Escritorio.
# =============================================================================

$ErrorActionPreference = 'Stop'

$src   = 'C:\laragon\www\cursalia'
$ver   = '3.3'
$name  = "cursalia-lms-v$ver"
$stage = Join-Path $env:TEMP $name
$out   = Join-Path ([Environment]::GetFolderPath('Desktop')) "$name.zip"

Write-Host "==> Preparando paquete de Cursalia LMS..." -ForegroundColor Green

# --- 0) Verificaciones previas -------------------------------------------------
if (-not (Test-Path (Join-Path $src 'vendor\autoload.php'))) {
    throw "Falta vendor/. Ejecuta primero:  composer install --no-dev --optimize-autoloader"
}
if (-not (Test-Path (Join-Path $src 'public\build'))) {
    throw "Falta public/build/. Ejecuta primero:  npm run build"
}

# --- 1) Limpiar staging previo -------------------------------------------------
if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
New-Item -ItemType Directory -Path $stage | Out-Null

# --- 2) Copiar el proyecto excluyendo lo pesado / sensible ---------------------
#  /E = subcarpetas (incl. vacías). Robocopy devuelve 0-7 en éxito, >=8 error.
$excludeDirs = @(
    (Join-Path $src '.git'),
    (Join-Path $src 'node_modules'),
    (Join-Path $src 'tests'),
    (Join-Path $src 'DOCUMENTOS'),
    (Join-Path $src '.github'),
    (Join-Path $src '.idea'),
    (Join-Path $src '.vscode'),
    (Join-Path $src 'public\storage')   # symlink: lo recrea el instalador
)
$excludeFiles = @(
    '.env', '.env.AWAY', '.env.DEVBAK', '.env.backup',
    '.env.local', '.env.production',
    '*.log', '.phpunit.result.cache', 'database.sqlite',
    'installed', 'build-package.ps1', 'hot', '*.zip'
)

Write-Host "==> Copiando archivos (esto puede tardar por vendor/)..." -ForegroundColor Green
robocopy $src $stage /E /XD $excludeDirs /XF $excludeFiles /NFL /NDL /NJH /NJS /NP | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy fallo con codigo $LASTEXITCODE" }
$global:LASTEXITCODE = 0

# --- 3) Limpieza fina dentro del staging --------------------------------------
Write-Host "==> Limpiando cachés y restos..." -ForegroundColor Green

# Caché de config/rutas compilada: PELIGROSA en un paquete (sobreescribiría el
# .env del usuario). La eliminamos siempre.
foreach ($f in @('config.php', 'routes-v7.php', 'routes.php')) {
    $p = Join-Path $stage "bootstrap\cache\$f"
    if (Test-Path $p) { Remove-Item $p -Force }
}

# Vaciar logs, sesiones, vistas y cache compiladas (manteniendo la estructura).
$toEmpty = @(
    'storage\logs',
    'storage\framework\cache\data',
    'storage\framework\sessions',
    'storage\framework\views'
)
foreach ($dir in $toEmpty) {
    $p = Join-Path $stage $dir
    if (Test-Path $p) {
        Get-ChildItem $p -File -Recurse | Where-Object { $_.Name -ne '.gitignore' } | Remove-Item -Force -ErrorAction SilentlyContinue
    }
}

# Quitar el archivo de bloqueo del instalador y el hot de Vite, por si acaso.
foreach ($f in @('storage\installed', 'public\hot')) {
    $p = Join-Path $stage $f
    if (Test-Path $p) { Remove-Item $p -Force }
}

# --- 4) Comprimir --------------------------------------------------------------
#  IMPORTANTE: NO usamos Compress-Archive porque (a) envuelve todo en una carpeta
#  con el nombre del staging (obliga a "aplanar" al extraer) y (b) escribe las
#  rutas con "\" de Windows (unzip en Linux avisa de backslashes). Creamos el ZIP
#  a mano: entradas en la RAÍZ (sin envoltorio) y con barras "/".
Write-Host "==> Comprimiendo a ZIP (raiz limpia, rutas con /)..." -ForegroundColor Green
if (Test-Path $out) { Remove-Item $out -Force }

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$stageRoot = (Resolve-Path $stage).Path.TrimEnd('\') + '\'
$zip = [System.IO.Compression.ZipFile]::Open($out, [System.IO.Compression.ZipArchiveMode]::Create)
try {
    # -Force para incluir ocultos (.env.example, .htaccess, .gitignore...).
    Get-ChildItem -Path $stage -Recurse -File -Force | ForEach-Object {
        $rel = $_.FullName.Substring($stageRoot.Length) -replace '\\', '/'
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
            $zip, $_.FullName, $rel, [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
    }
} finally {
    $zip.Dispose()
}

# --- 5) Limpiar staging --------------------------------------------------------
Remove-Item $stage -Recurse -Force

$sizeMB = [math]::Round((Get-Item $out).Length / 1MB, 1)
Write-Host ""
Write-Host "OK  Paquete creado:" -ForegroundColor Green
Write-Host "    $out  ($sizeMB MB)" -ForegroundColor White
Write-Host ""
Write-Host "Subdominio  (doc root -> /public):  abre  https://tudominio.com/install.php" -ForegroundColor Cyan
Write-Host "Dominio principal (sube a public_html / carpeta del dominio):  abre  https://tudominio.com/" -ForegroundColor Cyan
Write-Host "Tip: extrae con 'unzip' por Terminal (mas fiable que el File Manager con 11k archivos)." -ForegroundColor DarkGray
