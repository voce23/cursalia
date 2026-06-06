@extends('layouts.admin')

@section('title', $template->exists ? 'Editar plantilla' : 'Nueva plantilla')
@section('page-title', $template->exists ? 'Editar plantilla' : 'Nueva plantilla')
@section('page-subtitle', $template->title ?: 'Crea un nuevo producto digital del marketplace')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.templates.index') }}" class="hover:text-brand-700">Plantillas</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $template->exists ? $template->title : 'Nueva' }}</span>
</nav>

<form method="POST"
      action="{{ $template->exists ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
      enctype="multipart/form-data"
      class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    @csrf
    @if ($template->exists)@method('PUT')@endif

    {{-- ═══ Columna izquierda · contenido ═══ --}}
    <div class="space-y-6">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
            <h2 class="font-display font-extrabold text-lg text-ink-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-tag text-brand-600"></i> Contenido
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Título</label>
                    <input type="text" name="title" value="{{ old('title', $template->title) }}" required maxlength="120" autofocus
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Subtítulo / headline</label>
                    <input type="text" name="headline" value="{{ old('headline', $template->headline) }}" maxlength="200"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Descripción (HTML)</label>
                    <textarea name="description" rows="8"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y">{{ old('description', $template->description) }}</textarea>
                    <p class="text-xs text-ink-400 mt-1.5">Acepta HTML básico (p, ul, li, strong, a…)</p>
                </div>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
            <h2 class="font-display font-extrabold text-lg text-ink-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-coral-500"></i> Tech stack & Features
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Tech stack <span class="text-ink-400 text-xs">(una por línea)</span></label>
                    <textarea name="tech_stack_raw" rows="6"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y"
                        placeholder="Laravel 13&#10;Tailwind 4&#10;Alpine.js">{{ old('tech_stack_raw', implode("\n", $template->tech_stack ?? [])) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Features <span class="text-ink-400 text-xs">(una por línea)</span></label>
                    <textarea name="features_raw" rows="6"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y"
                        placeholder="Catálogo completo de cursos&#10;Auth de estudiantes...">{{ old('features_raw', implode("\n", $template->features ?? [])) }}</textarea>
                </div>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
            <h2 class="font-display font-extrabold text-lg text-ink-900 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-link text-sun-500"></i> Enlaces
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Demo URL</label>
                    <input type="url" name="demo_url" value="{{ old('demo_url', $template->demo_url) }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Descarga URL (FREE)</label>
                    <input type="url" name="download_url" value="{{ old('download_url', $template->download_url) }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
                </div>
            </div>
        </section>
    </div>

    {{-- ═══ Columna derecha · settings ═══ --}}
    <aside class="space-y-5 lg:sticky lg:top-24">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Estado</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <option value="draft"     @selected(old('status', $template->status) === 'draft')>Borrador</option>
                    <option value="published" @selected(old('status', $template->status) === 'published')>Publicada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Categoría</label>
                <select name="template_category_id" class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <option value="">— Sin categoría —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('template_category_id', $template->template_category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Versión</label>
                <input type="text" name="version" value="{{ old('version', $template->version) }}" required maxlength="20"
                    class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Orden</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $template->sort_order ?? 0) }}" min="0"
                    class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $template->is_featured))
                       class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Destacada (aparece arriba)</span>
            </label>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm">Precio</h3>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $template->is_free))
                       class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Es gratuita</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-ink-700 mb-1">Precio normal</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $template->price ?? 0) }}" required
                        class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink-700 mb-1">Oferta</label>
                    <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', $template->discount) }}"
                        class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
            <h3 class="font-display font-bold text-ink-900 text-sm mb-3">Thumbnail</h3>
            @if ($template->thumbnail)
                <img src="{{ asset('storage/'.$template->thumbnail) }}" alt="" class="w-full aspect-[16/10] rounded-xl object-cover border border-ink-200 mb-3">
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full text-xs text-ink-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
        </section>

        {{-- Actions --}}
        <div class="space-y-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> {{ $template->exists ? 'Guardar' : 'Crear plantilla' }}
            </button>
            <a href="{{ route('admin.templates.index') }}" class="block text-center px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">
                Cancelar
            </a>
        </div>
    </aside>
</form>

@endsection
