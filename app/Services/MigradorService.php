<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use ZipArchive;

/**
 * Migrador (complemento PRO estilo Duplicator): empaqueta TODO el sitio
 * (código CON vendor + base de datos) en un único ZIP, para revivirlo en
 * otro hosting o clonarlo a otro dominio con el instalador web.
 *
 * Vuelca la base de datos en PHP puro (sin depender de mysqldump). Las
 * subidas del cliente viven en storage/app/public, que ya entra dentro de
 * codigo/, así que el instalador solo recrea el enlace public/storage.
 */
class MigradorService
{
    /** Crea el paquete de migración y devuelve la ruta absoluta del ZIP. */
    public function buildPackage(): string
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '768M');

        $dir = $this->packageDir();
        File::ensureDirectoryExists($dir);

        // Borrar paquetes previos (solo guardamos el más reciente).
        foreach (glob($dir.DIRECTORY_SEPARATOR.'migrador-paquete-*.zip') ?: [] as $old) {
            @unlink($old);
        }

        $stamp = now()->format('Y-m-d_His');
        $zipPath = $dir.DIRECTORY_SEPARATOR."migrador-paquete-{$stamp}.zip";

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("No se pudo crear el archivo ZIP en {$zipPath}");
        }

        $zip->addFromString('database.sql', $this->dumpDatabase());
        $this->addCode($zip);
        $zip->addFromString('manifest.json', json_encode([
            'app' => config('app.name'),
            'generado' => now()->toDateTimeString(),
            'db' => DB::connection()->getDatabaseName(),
            'php_min' => '8.3',
            'incluye_vendor' => true,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $zip->close();

        return $zipPath;
    }

    /** Vuelca toda la base de datos a SQL restaurable (PHP puro vía PDO). */
    public function dumpDatabase(): string
    {
        $pdo = DB::connection()->getPdo();
        $database = DB::connection()->getDatabaseName();

        $sql = "-- Respaldo de la base de datos `{$database}` (Cursalia)\n";
        $sql .= '-- Generado: '.now()->toDateTimeString()."\n";
        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $tables = [];
        $views = [];
        foreach ($pdo->query('SHOW FULL TABLES', \PDO::FETCH_NUM) as $row) {
            if (($row[1] ?? '') === 'VIEW') {
                $views[] = $row[0];
            } else {
                $tables[] = $row[0];
            }
        }

        foreach ($tables as $table) {
            $sql .= $this->dumpTable($pdo, $table);
        }

        foreach ($views as $view) {
            $create = $pdo->query("SHOW CREATE VIEW `{$view}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "\n-- Vista: {$view}\n";
            $sql .= "DROP VIEW IF EXISTS `{$view}`;\n";
            $sql .= ($create['Create View'] ?? '').";\n";
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /** Estructura + datos de una tabla. */
    private function dumpTable(\PDO $pdo, string $table): string
    {
        $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);

        $sql = "\n-- ----------------------------\n";
        $sql .= "-- Tabla: {$table}\n";
        $sql .= "-- ----------------------------\n";
        $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $sql .= ($create['Create Table'] ?? '').";\n\n";

        $stmt = $pdo->query("SELECT * FROM `{$table}`");
        $rowCount = 0;

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $values = array_map(function ($value) use ($pdo) {
                if ($value === null) {
                    return 'NULL';
                }

                return $pdo->quote((string) $value);
            }, array_values($row));

            $columns = '`'.implode('`, `', array_keys($row)).'`';
            $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES (".implode(', ', $values).");\n";
            $rowCount++;
        }

        if ($rowCount === 0) {
            $sql .= "-- (sin datos)\n";
        }

        return $sql;
    }

    /**
     * Añade el código de la app bajo codigo/, INCLUYENDO vendor (para hosting
     * vacío sin Composer). Solo descarta lo regenerable/basura y los enlaces
     * (public/storage es un enlace; su contenido real va en storage/app/public).
     */
    private function addCode(ZipArchive $zip): void
    {
        $root = rtrim(base_path(), '/\\');

        $skip = [
            'node_modules', '.git',
            'storage/framework', 'storage/logs', 'storage/app/migrador',
            'public/storage',
        ];

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iter as $file) {
            $rel = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($root))), '/');

            foreach ($skip as $s) {
                if ($rel === $s || str_starts_with($rel, $s.'/')) {
                    continue 2;
                }
            }

            // Solo archivos reales: descarta carpetas, symlinks y junctions de Windows.
            if (! $file->isFile()) {
                continue;
            }

            $zip->addFile($file->getPathname(), 'codigo/'.$rel);
        }
    }

    public function packageDir(): string
    {
        return storage_path('app/migrador');
    }

    /**
     * @return array<int, array{name:string, path:string, size:int, mtime:int}>
     */
    public function listPackages(): array
    {
        $dir = $this->packageDir();
        if (! is_dir($dir)) {
            return [];
        }

        $files = [];
        foreach (glob($dir.DIRECTORY_SEPARATOR.'migrador-paquete-*.zip') ?: [] as $path) {
            $files[] = [
                'name' => basename($path),
                'path' => $path,
                'size' => filesize($path) ?: 0,
                'mtime' => filemtime($path) ?: 0,
            ];
        }

        usort($files, fn ($a, $b) => $b['mtime'] <=> $a['mtime']);

        return $files;
    }

    /** Resuelve de forma segura el nombre de un paquete a su ruta absoluta. */
    public function resolvePackage(string $name): ?string
    {
        $name = basename($name);
        if (! preg_match('/^migrador-paquete-[\w\-]+\.zip$/', $name)) {
            return null;
        }

        $path = $this->packageDir().DIRECTORY_SEPARATOR.$name;

        return is_file($path) ? $path : null;
    }

    /** Ruta del instalador web (plantilla servida al administrador). */
    public function installerPath(): string
    {
        return resource_path('migrador/instalador.php');
    }
}
