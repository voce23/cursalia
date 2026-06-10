<?php

namespace App\View\Components;

use App\Services\ImageOptimizer;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

/**
 * <x-image> · componente único para imágenes optimizadas.
 *
 * Uso básico:
 *   <x-image :src="$blog->thumbnail" alt="..." class="rounded-2xl" />
 *
 * Uso completo (con responsive):
 *   <x-image
 *     :src="$blog->thumbnail"
 *     alt="..."
 *     class="rounded-3xl"
 *     :sizes="['480, 800, 1200']"
 *     viewport-sizes="(min-width: 1024px) 1200px, 100vw"
 *     :width="1200"
 *     :height="630"
 *     :eager="true"
 *   />
 *
 * Comportamiento:
 *   - Si src es SVG → emite <img> directo con loading lazy.
 *   - Si src es raster → emite <picture> con sources AVIF + WebP + fallback,
 *     incluye width/height (anti-CLS), loading="lazy" (anti-bloqueo) por defecto,
 *     decoding="async".
 *   - Si el ImageOptimizer ya generó variantes responsive → emite srcset.
 *
 * Si recibe una URL absoluta (https://...) la emite tal cual como <img>.
 */
class Image extends Component
{
    public ?array $variants = null;

    public ?int $w = null;

    public ?int $h = null;

    public bool $isSvg = false;

    public bool $isExternal = false;

    public function __construct(
        public ?string $src = null,
        public string $alt = '',
        public string $class = '',
        public ?int $width = null,
        public ?int $height = null,
        public bool $eager = false,
        public string $sizes = '100vw',          // sizes attr para srcset
        public string $fit = 'cover',            // cover | contain
        public array $responsive = [480, 800, 1200],
    ) {
        if (! $this->src) {
            return;
        }

        // m2 · advertencia en debug si el componente recibe src pero alt vacío:
        // las imágenes con contenido (no decorativas) DEBEN tener alt descriptivo
        // para accesibilidad WCAG y para SEO de búsqueda por imágenes.
        if (config('app.debug') && trim($this->alt) === '') {
            Log::warning('<x-image> sin alt text', [
                'src' => $this->src,
            ]);
        }

        // URL absoluta externa → render directo.
        if (preg_match('#^https?://#i', $this->src)) {
            $this->isExternal = true;
            $this->w = $this->width;
            $this->h = $this->height;

            return;
        }

        $this->isSvg = str_ends_with(strtolower($this->src), '.svg');

        /** @var ImageOptimizer $opt */
        $opt = app(ImageOptimizer::class);

        if (! $this->isSvg) {
            $this->variants = $opt->variantsOf($this->src, $this->responsive);
        }

        // Dimensiones: si me las pasaron, las respeto; si no, leo del archivo.
        if ($this->width && $this->height) {
            $this->w = $this->width;
            $this->h = $this->height;
        } else {
            [$dw, $dh] = $opt->dimensionsOf($this->src);
            $this->w = $this->width ?: $dw;
            $this->h = $this->height ?: $dh;
        }
    }

    public function fitClass(): string
    {
        return $this->isSvg
            ? ($this->fit === 'contain' ? 'object-contain' : 'object-contain') // SVG siempre contain
            : ($this->fit === 'contain' ? 'object-contain' : 'object-cover');
    }

    public function loading(): string
    {
        return $this->eager ? 'eager' : 'lazy';
    }

    public function srcAbsolute(): string
    {
        if ($this->isExternal) {
            return $this->src;
        }

        return asset('storage/'.$this->src);
    }

    public function render(): View|Closure|string
    {
        return view('components.image');
    }
}
