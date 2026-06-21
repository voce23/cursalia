<?php

namespace App\Support;

use App\Models\AboutSection;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Complemento PRO: optimiza las imágenes del LMS convirtiéndolas a WebP
 * (formato moderno mucho más ligero) y redimensionándolas si superan un ancho
 * máximo. Solo GD (sin librerías externas), así funciona en cualquier hosting.
 *
 * Las imágenes del LMS viven en el disco `public` (storage/app/public) y se
 * muestran con asset('storage/'.$campo); aquí actualizamos el campo al .webp.
 */
class ImageOptimizer
{
    public const MAX_WIDTH = 1200;

    public const QUALITY = 82;

    /** Modelos y campos de imagen que conviene optimizar (contenido visible). */
    private const TARGETS = [
        [Course::class, 'thumbnail'],
        [CourseCategory::class, 'image'],
        [Blog::class, 'thumbnail'],
        [Brand::class, 'logo'],
        [AboutSection::class, 'image'],
    ];

    /**
     * Convierte una imagen a WebP optimizado junto al original.
     * Devuelve la ruta ABSOLUTA del .webp, o null si no se pudo.
     */
    public static function toWebp(string $absPath, int $maxWidth = self::MAX_WIDTH, int $quality = self::QUALITY): ?string
    {
        if (! is_file($absPath) || ! function_exists('imagewebp')) {
            return null;
        }

        $info = @getimagesize($absPath);
        if (! $info) {
            return null;
        }

        [$w, $h] = $info;
        $src = match ($info['mime'] ?? '') {
            'image/jpeg' => @imagecreatefromjpeg($absPath),
            'image/png' => @imagecreatefrompng($absPath),
            'image/webp' => @imagecreatefromwebp($absPath),
            'image/gif' => @imagecreatefromgif($absPath),
            default => null,
        };
        if (! $src) {
            return null;
        }

        $nw = $w;
        $nh = $h;
        if ($w > $maxWidth) {
            $nw = $maxWidth;
            $nh = (int) round($h * $maxWidth / $w);
        }

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);   // preservar transparencia (PNG → WebP)
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        $webpPath = preg_replace('/\.\w+$/', '', $absPath).'.webp';
        $ok = @imagewebp($dst, $webpPath, $quality);

        imagedestroy($src);
        imagedestroy($dst);

        return ($ok && is_file($webpPath)) ? $webpPath : null;
    }

    /**
     * Optimiza todas las imágenes de contenido del LMS.
     *
     * @return array{count:int, saved:int, errors:int}
     */
    public static function optimizeAll(?callable $onEach = null): array
    {
        $total = ['count' => 0, 'saved' => 0, 'errors' => 0];

        foreach (self::TARGETS as [$model, $field]) {
            if (! class_exists($model)) {
                continue;
            }
            // Saltar con seguridad si la columna no existe en esta versión del LMS.
            if (! Schema::hasColumn((new $model)->getTable(), $field)) {
                continue;
            }
            try {
                $r = self::optimizeModelImages($model, $field, $onEach);
                $total['count'] += $r['count'];
                $total['saved'] += $r['saved'];
                $total['errors'] += $r['errors'];
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $total;
    }

    /**
     * Optimiza el campo-imagen de un modelo: convierte a WebP (en el disco
     * public), actualiza la BD y borra el original.
     *
     * @return array{count:int, saved:int, errors:int}
     */
    public static function optimizeModelImages(string $modelClass, string $field, ?callable $onEach = null): array
    {
        $count = 0;
        $saved = 0;
        $errors = 0;
        $disk = Storage::disk('public');

        $modelClass::whereNotNull($field)->where($field, '!=', '')
            ->each(function ($m) use ($field, $disk, &$count, &$saved, &$errors, $onEach) {
                $rel = ltrim((string) $m->{$field}, '/');
                $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));

                // Solo fotos rasterizadas (jpg/png). Se ignoran SVG (vectorial),
                // WebP (ya hecho), GIF (animaciones), URLs externas y vacíos.
                if ($rel === '' || str_contains($rel, '://') || ! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                    return;
                }

                $abs = $disk->path($rel);
                if (! is_file($abs)) {
                    // Quizá ya se convirtió: apuntar al .webp si existe.
                    $webpRel = preg_replace('/\.\w+$/', '.webp', $rel);
                    if ($webpRel !== $rel && is_file($disk->path($webpRel))) {
                        $m->forceFill([$field => $webpRel])->save();
                    }

                    return;
                }

                try {
                    $before = filesize($abs) ?: 0;
                    $webpAbs = self::toWebp($abs);
                    if (! $webpAbs) {
                        $errors++;

                        return;
                    }
                    $after = filesize($webpAbs) ?: 0;

                    // No reemplazar si el WebP NO es más liviano (nunca engordar).
                    if ($after >= $before) {
                        @unlink($webpAbs);

                        return;
                    }

                    $dir = trim(str_replace('\\', '/', dirname($rel)), '/');
                    $newRel = ($dir === '' || $dir === '.') ? basename($webpAbs) : $dir.'/'.basename($webpAbs);

                    $m->forceFill([$field => $newRel])->save();
                    if ($webpAbs !== $abs) {
                        @unlink($abs);
                    }

                    $count++;
                    $saved += max(0, $before - $after);
                    if ($onEach) {
                        $onEach($newRel, $before, $after);
                    }
                } catch (Throwable $e) {
                    report($e);
                    $errors++;
                }
            });

        return ['count' => $count, 'saved' => $saved, 'errors' => $errors];
    }
}
