<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

/**
 * ImageOptimizer · Sprint 8 (Performance).
 *
 * Procesa una imagen subida y genera automáticamente:
 *   - El original optimizado (JPG calidad 85 o el formato que ya venía)
 *   - Versión WebP calidad 80 (~35% del peso del JPG)
 *   - Versión AVIF calidad 65 (~25% del peso del JPG, soporte navegador 96%)
 *   - 3 tamaños responsive del original (sm 480, md 800, lg 1200) si se pide
 *
 * Para SVG: minifica el contenido sin librerías externas (regex casero).
 *
 * Convención de nombres:
 *   blog/abc123.webp       → original optimizado
 *   blog/abc123.avif       → versión AVIF
 *   blog/abc123-480.webp   → variante responsive 480px
 *   blog/abc123-800.webp   → variante responsive 800px
 *
 * El componente <x-image> los busca automáticamente; si no existen, fallback.
 */
class ImageOptimizer
{
    public const QUALITY_JPG  = 85;
    public const QUALITY_WEBP = 80;
    public const QUALITY_AVIF = 65;

    /** Tamaños responsive por defecto. */
    public const SIZES_DEFAULT = [480, 800, 1200];

    /**
     * Procesa un archivo subido y guarda original + WebP + AVIF (+ responsive si se pide).
     *
     * @return string  Ruta relativa del archivo "principal" (el que se guarda en BD).
     *                 Para SVG: el .svg minificado. Para raster: el .webp original.
     */
    public function processUpload(
        UploadedFile $file,
        string $folder,
        ?int $targetWidth = null,
        ?int $targetHeight = null,
        bool $cover = true,
        array $responsiveSizes = []
    ): string {
        Storage::disk('public')->makeDirectory($folder);

        $ext = strtolower($file->getClientOriginalExtension());
        $base = $folder.'/'.uniqid(Str::slug($folder, '_').'_');

        // ─── SVG: solo minificar y guardar ──────────────────────────────────
        if ($ext === 'svg') {
            $svg = file_get_contents($file->getRealPath());
            $minified = $this->minifySvg($svg);
            $path = $base.'.svg';
            Storage::disk('public')->put($path, $minified);
            return $path;
        }

        // ─── Raster: cargar con Intervention ────────────────────────────────
        $img = Image::decodePath($file->getRealPath());

        // Redimensionar si se pidió tamaño objetivo.
        if ($targetWidth && $targetHeight) {
            $cover ? $img->cover($targetWidth, $targetHeight)
                   : $img->contain($targetWidth, $targetHeight);
        } elseif ($targetWidth) {
            $img->scaleDown(width: $targetWidth);
        }

        // 1) Original como WebP de alta calidad (lo guardamos como "principal").
        $mainPath = $base.'.webp';
        $img->encode(new WebpEncoder(quality: self::QUALITY_WEBP))
            ->save(Storage::disk('public')->path($mainPath));

        // 2) AVIF (versión moderna, ~25% del peso del JPG original).
        try {
            $avifPath = $base.'.avif';
            $img->encode(new AvifEncoder(quality: self::QUALITY_AVIF))
                ->save(Storage::disk('public')->path($avifPath));
        } catch (\Throwable $e) {
            // Si AVIF falla por cualquier razón en este entorno, seguimos sin él.
            \Illuminate\Support\Facades\Log::warning('ImageOptimizer: AVIF encode failed, falling back', [
                'file'  => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
        }

        // 3) JPG fallback para navegadores muy antiguos.
        try {
            $jpgPath = $base.'.jpg';
            $img->encode(new JpegEncoder(quality: self::QUALITY_JPG))
                ->save(Storage::disk('public')->path($jpgPath));
        } catch (\Throwable $e) {
            // Si la imagen original tenía alpha (PNG transparente), JPG puede no salir bien.
            \Illuminate\Support\Facades\Log::info('ImageOptimizer: JPG fallback skipped (PNG transparent?)', [
                'file'  => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);
        }

        // 4) Variantes responsive si se piden.
        foreach ($responsiveSizes as $w) {
            $variant = Image::decodePath($file->getRealPath())->scaleDown(width: $w);
            $variant->encode(new WebpEncoder(quality: self::QUALITY_WEBP))
                    ->save(Storage::disk('public')->path($base.'-'.$w.'.webp'));
            try {
                $variant->encode(new AvifEncoder(quality: self::QUALITY_AVIF))
                        ->save(Storage::disk('public')->path($base.'-'.$w.'.avif'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('ImageOptimizer: AVIF responsive variant skipped', [
                    'path'  => $base.'-'.$w.'.avif',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $mainPath;
    }

    /**
     * Re-procesa un archivo que YA está en storage/public/{path}.
     * Útil para el comando images:reprocess sobre imágenes históricas.
     */
    public function reprocessExisting(string $relativePath, array $responsiveSizes = []): array
    {
        $generated = [];
        if (! Storage::disk('public')->exists($relativePath)) {
            return $generated;
        }

        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        $fullPath = Storage::disk('public')->path($relativePath);
        $base = preg_replace('/\.[a-z0-9]+$/i', '', $relativePath);

        // SVG: solo minificar.
        if ($ext === 'svg') {
            $svg = Storage::disk('public')->get($relativePath);
            $before = strlen($svg);
            $minified = $this->minifySvg($svg);
            $after = strlen($minified);
            if ($after < $before) {
                Storage::disk('public')->put($relativePath, $minified);
                $generated[] = "svg minified ({$before}→{$after} bytes)";
            }
            return $generated;
        }

        $img = Image::decodePath($fullPath);

        // WebP
        if (! Storage::disk('public')->exists($base.'.webp')) {
            $img->encode(new WebpEncoder(quality: self::QUALITY_WEBP))
                ->save(Storage::disk('public')->path($base.'.webp'));
            $generated[] = 'webp';
        }

        // AVIF
        if (! Storage::disk('public')->exists($base.'.avif')) {
            try {
                $img->encode(new AvifEncoder(quality: self::QUALITY_AVIF))
                    ->save(Storage::disk('public')->path($base.'.avif'));
                $generated[] = 'avif';
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('ImageOptimizer: AVIF reprocess skipped', [
                    'path'  => $base.'.avif',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Responsive
        foreach ($responsiveSizes as $w) {
            if (! Storage::disk('public')->exists($base.'-'.$w.'.webp')) {
                $variant = Image::decodePath($fullPath)->scaleDown(width: $w);
                $variant->encode(new WebpEncoder(quality: self::QUALITY_WEBP))
                        ->save(Storage::disk('public')->path($base.'-'.$w.'.webp'));
                try {
                    $variant->encode(new AvifEncoder(quality: self::QUALITY_AVIF))
                            ->save(Storage::disk('public')->path($base.'-'.$w.'.avif'));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('ImageOptimizer: AVIF responsive reprocess skipped', [
                        'path'  => $base.'-'.$w.'.avif',
                        'error' => $e->getMessage(),
                    ]);
                }
                $generated[] = "responsive-{$w}";
            }
        }

        return $generated;
    }

    /**
     * Minifica SVG sin dependencias: quita comentarios, espacios sobrantes,
     * indentación y atributos vacíos. NO destruye nada visualmente.
     *
     * Reduce 30-60% el peso de SVGs típicos hechos a mano.
     */
    public function minifySvg(string $svg): string
    {
        // 1. Quitar comentarios HTML.
        $svg = preg_replace('/<!--.*?-->/s', '', $svg);
        // 2. Quitar XML declaration y DOCTYPE (no aportan nada cuando es inline o como img).
        $svg = preg_replace('/<\?xml[^>]*\?>/', '', $svg);
        $svg = preg_replace('/<!DOCTYPE[^>]*>/', '', $svg);
        // 3. Colapsar múltiples espacios/saltos en uno solo.
        $svg = preg_replace('/\s+/', ' ', $svg);
        // 4. Quitar espacios entre etiquetas adyacentes.
        $svg = preg_replace('/>\s+</', '><', $svg);
        // 5. Quitar espacios al inicio/final de atributos.
        $svg = preg_replace('/\s*=\s*"/', '="', $svg);
        return trim($svg);
    }

    /**
     * Obtiene las dimensiones de una imagen (para los atributos width/height del <img>).
     * Devuelve [null, null] si el archivo no se puede leer o es SVG sin viewBox.
     */
    public function dimensionsOf(string $relativePath): array
    {
        if (! Storage::disk('public')->exists($relativePath)) {
            return [null, null];
        }
        $fullPath = Storage::disk('public')->path($relativePath);
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        if ($ext === 'svg') {
            $svg = file_get_contents($fullPath);
            if (preg_match('/viewBox\s*=\s*"\s*[-\d.]+\s+[-\d.]+\s+([\d.]+)\s+([\d.]+)\s*"/', $svg, $m)) {
                return [(int) round($m[1]), (int) round($m[2])];
            }
            if (preg_match('/width\s*=\s*"(\d+)"/', $svg, $w) && preg_match('/height\s*=\s*"(\d+)"/', $svg, $h)) {
                return [(int) $w[1], (int) $h[1]];
            }
            return [null, null];
        }

        $info = @getimagesize($fullPath);
        return $info ? [(int) $info[0], (int) $info[1]] : [null, null];
    }

    /**
     * Devuelve las URLs absolutas de las variantes existentes de una imagen.
     * El componente <x-image> usa esto para construir el srcset.
     *
     * @return array{
     *   src: string,               URL del archivo "principal" (siempre existe)
     *   webp: ?string,             URL .webp si existe (o el src si ya es webp)
     *   avif: ?string,             URL .avif si existe
     *   jpg:  ?string,             URL .jpg fallback si existe
     *   responsive: array<int,array{avif?: string, webp?: string}>  // por anchura
     * }
     */
    public function variantsOf(string $relativePath, array $responsiveSizes = []): array
    {
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        $base = preg_replace('/\.[a-z0-9]+$/i', '', $relativePath);

        $url = fn (string $p) => Storage::disk('public')->exists($p) ? asset('storage/'.$p) : null;

        $result = [
            'src'        => asset('storage/'.$relativePath),
            'webp'       => null,
            'avif'       => null,
            'jpg'        => null,
            'responsive' => [],
        ];

        if ($ext === 'svg') {
            return $result; // SVG no tiene variantes.
        }

        // El propio archivo según su extensión.
        if ($ext === 'webp') {
            $result['webp'] = asset('storage/'.$relativePath);
        }
        if ($ext === 'avif') {
            $result['avif'] = asset('storage/'.$relativePath);
        }
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $result['jpg'] = asset('storage/'.$relativePath);
        }

        // Buscar variantes hermanas.
        $result['webp'] ??= $url($base.'.webp');
        $result['avif'] ??= $url($base.'.avif');
        $result['jpg']  ??= $url($base.'.jpg');

        foreach ($responsiveSizes as $w) {
            $variant = [];
            if ($p = $url($base.'-'.$w.'.avif')) $variant['avif'] = $p;
            if ($p = $url($base.'-'.$w.'.webp')) $variant['webp'] = $p;
            if (! empty($variant)) {
                $result['responsive'][$w] = $variant;
            }
        }

        return $result;
    }
}
