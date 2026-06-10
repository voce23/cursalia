@props(['sectionImage' => null])
{{--
    Fondo de cabecera (hero) editable desde el admin.
    Prioridad: imagen propia de la sección → imagen global (Apariencia) → imagen fija incluida.
    Si no hay ninguna, no pinta nada y queda el degradado de la sección (white-label safe).
--}}
@php
    $path = $sectionImage ?: ($generalSetting->hero_image ?? null);
    $src = $path
        ? \Illuminate\Support\Facades\Storage::url($path)
        : (file_exists(public_path('img/hero-bg.jpg')) ? asset('img/hero-bg.jpg') : null);
@endphp
@if ($src)
    <img src="{{ $src }}" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-br from-ink-950/92 via-ink-950/85 to-brand-900/80"></div>
@endif
