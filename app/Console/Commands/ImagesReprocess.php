<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Blog;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Re-procesa imágenes históricas para generar variantes AVIF/WebP/responsive.
 *
 *   php artisan images:reprocess              → re-procesa todo
 *   php artisan images:reprocess --only=blog  → solo thumbnails de blog
 *   php artisan images:reprocess --only=avatars
 *   php artisan images:reprocess --only=svgs  → solo minifica SVGs
 *   php artisan images:reprocess --dry        → muestra qué haría, sin escribir
 */
class ImagesReprocess extends Command
{
    protected $signature = 'images:reprocess
        {--only= : Filtro: blog, avatars, svgs (vacío = todo)}
        {--dry : No escribe nada, solo muestra el plan}';

    protected $description = 'Genera AVIF/WebP/responsive de imágenes ya subidas, y minifica SVGs.';

    public function handle(ImageOptimizer $opt): int
    {
        $only = $this->option('only');
        $dry = (bool) $this->option('dry');

        if ($dry) {
            $this->warn('Modo DRY-RUN: no se escribirá nada.');
        }

        $tasks = [];

        // ─── Thumbnails de blog ──────────────────────────────────────────────
        if (! $only || in_array($only, ['blog', 'svgs'])) {
            $thumbs = Blog::whereNotNull('thumbnail')->pluck('thumbnail')->unique();
            foreach ($thumbs as $path) {
                $tasks[] = ['type' => 'blog', 'path' => $path, 'sizes' => [480, 800, 1200]];
            }
        }

        // ─── Avatares de admin ───────────────────────────────────────────────
        if (! $only || in_array($only, ['avatars', 'svgs'])) {
            $avatars = Admin::whereNotNull('image')->pluck('image')->unique();
            foreach ($avatars as $path) {
                $tasks[] = ['type' => 'avatar', 'path' => $path, 'sizes' => []];
            }
        }

        if (empty($tasks)) {
            $this->info('No hay imágenes para procesar.');

            return self::SUCCESS;
        }

        $this->line('');
        $this->info(count($tasks).' imágenes a procesar:');
        $this->line('');

        $stats = ['svg_min' => 0, 'webp' => 0, 'avif' => 0, 'responsive' => 0, 'skipped' => 0];
        $totalBefore = 0;
        $totalAfter = 0;

        foreach ($tasks as $t) {
            $path = $t['path'];
            $isSvg = str_ends_with(strtolower($path), '.svg');

            if ($only === 'svgs' && ! $isSvg) {
                $stats['skipped']++;

                continue;
            }

            // Tamaño antes
            $before = Storage::disk('public')->exists($path)
                ? Storage::disk('public')->size($path) : 0;
            $totalBefore += $before;

            if ($dry) {
                $this->line('  • '.$path.' ('.$this->bytes($before).')');

                continue;
            }

            $generated = $opt->reprocessExisting($path, $t['sizes']);

            if (empty($generated)) {
                $this->line('  • <fg=gray>'.$path.' (sin cambios)</>');
                $stats['skipped']++;
            } else {
                // Tamaño después: original + todas las variantes nuevas
                $base = preg_replace('/\.[a-z0-9]+$/i', '', $path);
                $after = $before;
                foreach (['.webp', '.avif', '.jpg'] as $ext) {
                    if (Storage::disk('public')->exists($base.$ext)) {
                        $after += Storage::disk('public')->size($base.$ext);
                    }
                }
                foreach ($t['sizes'] as $w) {
                    foreach (['.webp', '.avif'] as $ext) {
                        if (Storage::disk('public')->exists($base.'-'.$w.$ext)) {
                            $after += Storage::disk('public')->size($base.'-'.$w.$ext);
                        }
                    }
                }
                $totalAfter += $after;

                $this->line('  <fg=green>✓</> '.$path);
                foreach ($generated as $g) {
                    if (str_contains($g, 'svg')) {
                        $stats['svg_min']++;
                    } elseif ($g === 'webp') {
                        $stats['webp']++;
                    } elseif ($g === 'avif') {
                        $stats['avif']++;
                    } elseif (str_starts_with($g, 'responsive')) {
                        $stats['responsive']++;
                    }
                    $this->line('     · '.$g);
                }
            }
        }

        $this->line('');
        $this->line(str_repeat('─', 60));
        $this->info('Resumen:');
        $this->line('  SVG minificados:        '.$stats['svg_min']);
        $this->line('  WebP generados:         '.$stats['webp']);
        $this->line('  AVIF generados:         '.$stats['avif']);
        $this->line('  Variantes responsive:   '.$stats['responsive']);
        $this->line('  Sin cambios (ya hechos):'.$stats['skipped']);

        if (! $dry && $totalAfter > 0) {
            $this->line('');
            $this->line('  Espacio en disco: '.$this->bytes($totalBefore).' → '.$this->bytes($totalAfter));
            $this->comment('  (es MÁS espacio en disco, pero MUCHO MENOS ancho de banda al servir cada visita)');
        }

        return self::SUCCESS;
    }

    private function bytes(int $b): string
    {
        if ($b > 1048576) {
            return round($b / 1048576, 1).' MB';
        }
        if ($b > 1024) {
            return round($b / 1024, 1).' KB';
        }

        return $b.' B';
    }
}
