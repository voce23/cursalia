@extends('layouts.admin')

@section('title', 'Secciones del inicio')
@section('page-title', 'Secciones del inicio')
@section('page-subtitle', 'Edita los textos e imágenes de la página principal')

@section('content')

@php
    $inp = 'w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm';
    $lbl = 'block text-sm font-medium text-ink-700 mb-1.5';
    $card = 'bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4 scroll-mt-24';
    $save = 'inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition';
    $h2 = 'font-display font-extrabold text-lg text-ink-900 flex items-center gap-2';
@endphp

{{-- Navegación interna --}}
<div class="sticky top-[68px] z-10 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 mb-6 bg-cream/85 backdrop-blur border-b border-ink-200/70">
    <div class="flex flex-wrap gap-2 text-sm">
        <a href="#hero" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Portada</a>
        <a href="#features" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Razones</a>
        <a href="#categorias" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Categorías</a>
        <a href="#cursos" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Cursos</a>
        <a href="#about" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Sobre nosotros</a>
        <a href="#newsletter" class="px-3 py-1.5 rounded-full bg-white border border-ink-200 font-semibold text-ink-700 hover:border-brand-300">Newsletter / Video</a>
    </div>
</div>

<div class="space-y-8 max-w-4xl">

    {{-- ════════ HERO ════════ --}}
    <form id="hero" method="POST" action="{{ route('admin.home-sections.hero') }}" enctype="multipart/form-data" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-solid fa-image text-brand-600"></i> Portada (Hero)</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Etiqueta superior</label><input type="text" name="badge_text" value="{{ old('badge_text', $hero->badge_text) }}" maxlength="120" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Texto resaltado</label><input type="text" name="highlight_text" value="{{ old('highlight_text', $hero->highlight_text) }}" maxlength="120" class="{{ $inp }}"></div>
        </div>
        <div><label class="{{ $lbl }}">Título principal *</label><input type="text" name="title" value="{{ old('title', $hero->title) }}" required maxlength="255" class="{{ $inp }}">@error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror</div>
        <div><label class="{{ $lbl }}">Descripción</label><textarea name="description" rows="2" maxlength="1000" class="{{ $inp }} resize-y">{{ old('description', $hero->description) }}</textarea></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Botón principal · texto</label><input type="text" name="primary_button_text" value="{{ old('primary_button_text', $hero->primary_button_text) }}" maxlength="80" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Botón principal · URL</label><input type="text" name="primary_button_url" value="{{ old('primary_button_url', $hero->primary_button_url) }}" maxlength="255" class="{{ $inp }} font-mono"></div>
            <div><label class="{{ $lbl }}">Botón secundario · texto</label><input type="text" name="secondary_button_text" value="{{ old('secondary_button_text', $hero->secondary_button_text) }}" maxlength="80" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Botón secundario · URL</label><input type="text" name="secondary_button_url" value="{{ old('secondary_button_url', $hero->secondary_button_url) }}" maxlength="255" class="{{ $inp }} font-mono"></div>
        </div>
        <div>
            <label class="{{ $lbl }}">Imagen de portada (opcional)</label>
            <div class="flex items-center gap-4">
                @if ($hero->hero_image)<img src="{{ \Illuminate\Support\Facades\Storage::url($hero->hero_image) }}" alt="" class="w-20 h-14 rounded-xl object-cover border border-ink-200">@endif
                <input type="file" name="hero_image" accept="image/png,image/jpeg,image/webp" class="flex-1 text-sm text-ink-600 file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
            </div>
            @error('hero_image')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar portada</button>
    </form>

    {{-- ════════ FEATURES (4 razones) ════════ --}}
    <form id="features" method="POST" action="{{ route('admin.home-sections.features') }}" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-solid fa-star text-sun-500"></i> Razones / Ventajas</h2>
        @if ($features->isEmpty())
            <p class="text-sm text-ink-500">No hay razones configuradas. (Se está mostrando el texto por defecto en el inicio.)</p>
        @else
            @foreach ($features as $i => $f)
                <div class="rounded-2xl border border-ink-200/70 p-4 space-y-3">
                    <input type="hidden" name="features[{{ $i }}][id]" value="{{ $f->id }}">
                    <div class="grid sm:grid-cols-[1fr_1fr_auto] gap-3">
                        <div><label class="block text-xs text-ink-500 mb-1">Título *</label><input type="text" name="features[{{ $i }}][title]" value="{{ old('features.'.$i.'.title', $f->title) }}" required maxlength="120" class="{{ $inp }}"></div>
                        <div><label class="block text-xs text-ink-500 mb-1">Icono (Font Awesome)</label><input type="text" name="features[{{ $i }}][icon]" value="{{ old('features.'.$i.'.icon', $f->icon) }}" maxlength="80" placeholder="fa-solid fa-bolt" class="{{ $inp }} font-mono"></div>
                        <div><label class="block text-xs text-ink-500 mb-1">Orden</label><input type="number" name="features[{{ $i }}][sort_order]" value="{{ old('features.'.$i.'.sort_order', $f->sort_order ?: $i + 1) }}" min="1" max="999" class="{{ $inp }} w-24"></div>
                    </div>
                    <div><label class="block text-xs text-ink-500 mb-1">Descripción</label><textarea name="features[{{ $i }}][description]" rows="2" maxlength="500" class="{{ $inp }} resize-y">{{ old('features.'.$i.'.description', $f->description) }}</textarea></div>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" name="features[{{ $i }}][is_active]" value="1" @checked(old('features.'.$i.'.is_active', $f->is_active)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400"><span class="text-ink-700">Visible</span></label>
                </div>
            @endforeach
            <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar razones</button>
        @endif
    </form>

    {{-- ════════ CATEGORÍAS DESTACADAS ════════ --}}
    <form id="categorias" method="POST" action="{{ route('admin.home-sections.featured-categories') }}" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-solid fa-folder-tree text-brand-600"></i> Sección de categorías</h2>
        <div><label class="{{ $lbl }}">Título</label><input type="text" name="title" value="{{ old('title', $featuredCategorySection->title) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Subtítulo</label><input type="text" name="subtitle" value="{{ old('subtitle', $featuredCategorySection->subtitle) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Nº de categorías a mostrar *</label><input type="number" name="limit_items" value="{{ old('limit_items', $featuredCategorySection->limit_items) }}" required min="1" max="30" class="{{ $inp }} w-32">@error('limit_items')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror</div>
        <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
    </form>

    {{-- ════════ CURSOS DESTACADOS ════════ --}}
    <form id="cursos" method="POST" action="{{ route('admin.home-sections.latest-courses') }}" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-solid fa-graduation-cap text-coral-500"></i> Sección de cursos destacados</h2>
        <div><label class="{{ $lbl }}">Título</label><input type="text" name="title" value="{{ old('title', $latestCourseSection->title) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Subtítulo</label><input type="text" name="subtitle" value="{{ old('subtitle', $latestCourseSection->subtitle) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Nº de cursos a mostrar *</label><input type="number" name="limit_items" value="{{ old('limit_items', $latestCourseSection->limit_items) }}" required min="1" max="24" class="{{ $inp }} w-32">@error('limit_items')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror</div>
        <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
    </form>

    {{-- ════════ SOBRE NOSOTROS (compartida con /about) ════════ --}}
    <form id="about" method="POST" action="{{ route('admin.home-sections.about') }}" enctype="multipart/form-data" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-solid fa-circle-info text-brand-600"></i> Sobre nosotros</h2>
        <div class="rounded-xl bg-brand-50 border border-brand-200 text-brand-800 text-xs px-3 py-2">Este bloque se usa tanto en el inicio como en la página <strong>Nosotros</strong>.</div>
        <div><label class="{{ $lbl }}">Título</label><input type="text" name="title" value="{{ old('title', $aboutSection->title) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Subtítulo</label><input type="text" name="subtitle" value="{{ old('subtitle', $aboutSection->subtitle) }}" maxlength="255" class="{{ $inp }}"></div>
        <div><label class="{{ $lbl }}">Contenido (admite HTML básico)</label><textarea name="content" rows="5" class="{{ $inp }} resize-y">{{ old('content', $aboutSection->content) }}</textarea></div>
        <div>
            <label class="{{ $lbl }}">Valores (uno por línea) · solo se muestran en la página "Nosotros"</label>
            <textarea name="about_values" rows="4" maxlength="2000" placeholder="Aprender en comunidad&#10;Práctica antes que teoría&#10;Acceso para todos&#10;Crecimiento sostenible" class="{{ $inp }} resize-y">{{ old('about_values', $aboutSection->about_values) }}</textarea>
            <p class="text-xs text-ink-400 mt-1.5">Si lo dejas vacío, se muestran 4 valores por defecto. Los iconos se asignan automáticamente.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Botón · texto</label><input type="text" name="button_text" value="{{ old('button_text', $aboutSection->button_text) }}" maxlength="80" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Botón · URL</label><input type="text" name="button_url" value="{{ old('button_url', $aboutSection->button_url) }}" maxlength="255" class="{{ $inp }} font-mono"></div>
        </div>
        <div>
            <label class="{{ $lbl }}">Imagen (opcional)</label>
            <div class="flex items-center gap-4">
                @if ($aboutSection->image)<img src="{{ \Illuminate\Support\Facades\Storage::url($aboutSection->image) }}" alt="" class="w-20 h-14 rounded-xl object-cover border border-ink-200">@endif
                <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="flex-1 text-sm text-ink-600 file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
            </div>
            @error('image')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar sección</button>
    </form>

    {{-- ════════ NEWSLETTER / VIDEO ════════ --}}
    <form id="newsletter" method="POST" action="{{ route('admin.home-misc.update') }}" class="{{ $card }}">
        @csrf
        <h2 class="{{ $h2 }}"><i class="fa-regular fa-envelope text-coral-500"></i> Newsletter y video</h2>
        @php $misc = \App\Models\HomeMiscSection::query()->first(); @endphp
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="{{ $lbl }}">Newsletter · título</label><input type="text" name="newsletter_title" value="{{ old('newsletter_title', $misc?->newsletter_title) }}" maxlength="255" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Newsletter · subtítulo</label><input type="text" name="newsletter_subtitle" value="{{ old('newsletter_subtitle', $misc?->newsletter_subtitle) }}" maxlength="255" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Video · título de sección</label><input type="text" name="video_section_title" value="{{ old('video_section_title', $misc?->video_section_title) }}" maxlength="255" class="{{ $inp }}"></div>
            <div><label class="{{ $lbl }}">Video · URL (YouTube/Vimeo)</label><input type="text" name="video_url" value="{{ old('video_url', $misc?->video_url) }}" maxlength="255" class="{{ $inp }} font-mono"></div>
        </div>
        <button type="submit" class="{{ $save }}"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
    </form>

</div>

@endsection
