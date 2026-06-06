{{-- Componente x-image · Sprint Optimización Imágenes 2026 --}}
@php
    $imgClass = trim(($attributes->get('class') ?? '').' '.$class);
    $imgClass = trim($imgClass.' '.$fitClass());
    // Bg blanco automático para SVG (evita choque con el degradado del contenedor).
    if ($isSvg) {
        $imgClass = trim($imgClass.' bg-white');
    }
@endphp

@if (! $src)
    {{-- Sin src: placeholder accesible (mejor que <img> roto) --}}
    <span class="{{ $imgClass }} grid place-items-center bg-cream-2 text-ink-300"
          @if($w && $h) style="aspect-ratio: {{ $w }}/{{ $h }};" @endif
          aria-label="{{ $alt }}">
        <i class="fa-regular fa-image"></i>
    </span>

@elseif ($isSvg || $isExternal)
    {{-- SVG o URL externa: <img> simple, no hay variantes que ofrecer --}}
    <img src="{{ $srcAbsolute() }}"
         alt="{{ $alt }}"
         class="{{ $imgClass }}"
         loading="{{ $loading() }}"
         decoding="async"
         @if($w) width="{{ $w }}" @endif
         @if($h) height="{{ $h }}" @endif>

@else
    {{-- Raster con variantes: <picture> con AVIF + WebP + fallback --}}
    <picture>
        {{-- AVIF (mejor compresión, soporte 96% navegadores 2026) --}}
        @if (!empty($variants['responsive']))
            @php
                $avifSrcset = collect($variants['responsive'])
                    ->map(fn($v, $w) => isset($v['avif']) ? $v['avif'].' '.$w.'w' : null)
                    ->filter()
                    ->implode(', ');
                $webpSrcset = collect($variants['responsive'])
                    ->map(fn($v, $w) => isset($v['webp']) ? $v['webp'].' '.$w.'w' : null)
                    ->filter()
                    ->implode(', ');
            @endphp
            @if ($avifSrcset)
                <source type="image/avif" srcset="{{ $avifSrcset }}" sizes="{{ $sizes }}">
            @endif
            @if ($webpSrcset)
                <source type="image/webp" srcset="{{ $webpSrcset }}" sizes="{{ $sizes }}">
            @endif
        @else
            {{-- Sin variantes responsive: ofrecer AVIF y WebP sueltos si existen --}}
            @if (!empty($variants['avif']))
                <source type="image/avif" srcset="{{ $variants['avif'] }}">
            @endif
            @if (!empty($variants['webp']) && $variants['webp'] !== $srcAbsolute())
                <source type="image/webp" srcset="{{ $variants['webp'] }}">
            @endif
        @endif

        {{-- Fallback <img>: el src principal, JPG si existe, o el propio src --}}
        <img src="{{ $variants['jpg'] ?? $srcAbsolute() }}"
             alt="{{ $alt }}"
             class="{{ $imgClass }}"
             loading="{{ $loading() }}"
             decoding="async"
             @if($w) width="{{ $w }}" @endif
             @if($h) height="{{ $h }}" @endif>
    </picture>
@endif
