@extends('layouts.admin')

@section('title', $service->exists ? 'Editar servicio' : 'Nuevo servicio')
@section('page-title', $service->exists ? 'Editar servicio' : 'Nuevo servicio')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.services.index') }}" class="hover:text-brand-700">Servicios</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $service->exists ? $service->title : 'Nuevo' }}</span>
</nav>

<form method="POST"
      action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}"
      class="grid lg:grid-cols-[1fr_320px] gap-6 items-start">
    @csrf
    @if ($service->exists)@method('PUT')@endif

    <div class="space-y-6">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-tag text-brand-600"></i> Contenido
            </h2>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título</label>
                <input type="text" name="title" value="{{ old('title', $service->title) }}" required maxlength="120" autofocus
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Subtítulo</label>
                <input type="text" name="headline" value="{{ old('headline', $service->headline) }}" maxlength="200"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Descripción (HTML)</label>
                <textarea name="description" rows="5"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y">{{ old('description', $service->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Features (una por línea)</label>
                <textarea name="features_raw" rows="6"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y"
                    placeholder="Sesión 1 hora por Zoom o WhatsApp&#10;Auditoría rápida...">{{ old('features_raw', implode("\n", $service->features ?? [])) }}</textarea>
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-arrow-right text-coral-500"></i> CTA (botón)
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Texto del botón</label>
                    <input type="text" name="cta_text" value="{{ old('cta_text', $service->cta_text) }}" required maxlength="60"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">URL (vacío → form)</label>
                    <input type="url" name="cta_url" value="{{ old('cta_url', $service->cta_url) }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
                </div>
            </div>
            <p class="text-xs text-ink-400">Si dejas URL vacío, el botón hace scroll al formulario y preselecciona este servicio.</p>
        </section>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm">Precio</h3>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $service->is_free)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Es gratuito</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-ink-500 mb-1">Precio</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $service->price ?? 0) }}" required
                        class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-ink-500 mb-1">Moneda</label>
                    <input type="text" name="currency" value="{{ old('currency', $service->currency ?? 'USD') }}" required maxlength="8"
                        class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs text-ink-500 mb-1">Sufijo (ej. "por sesión")</label>
                <input type="text" name="price_suffix" value="{{ old('price_suffix', $service->price_suffix) }}" maxlength="40"
                    class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm">Apariencia</h3>
            <div class="grid grid-cols-[auto_1fr] gap-2 items-center">
                <input type="color" name="color" value="{{ old('color', $service->color ?? '#10B981') }}" class="w-11 h-11 rounded-2xl border border-ink-200 cursor-pointer">
                <input type="text" name="color_text" value="{{ old('color', $service->color ?? '#10B981') }}" disabled
                    class="px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 text-sm font-mono uppercase">
            </div>
            <div>
                <label class="block text-xs text-ink-500 mb-1">Icono Font Awesome</label>
                <input type="text" name="icon" value="{{ old('icon', $service->icon) }}" maxlength="80" placeholder="fa-solid fa-headset"
                    class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
            </div>
            <div>
                <label class="block text-xs text-ink-500 mb-1">Badge (ej. "Popular")</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $service->badge_text) }}" maxlength="40"
                    class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-3">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Activo (visible en /services)</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $service->is_featured)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Destacado (borde de color)</span>
            </label>
            <div>
                <label class="block text-xs text-ink-500 mb-1">Orden</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}" min="0"
                    class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
        </section>

        <div class="space-y-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> {{ $service->exists ? 'Guardar' : 'Crear servicio' }}
            </button>
            <a href="{{ route('admin.services.index') }}" class="block text-center px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
        </div>
    </aside>
</form>

@endsection
