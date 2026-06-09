@php $item = $item ?? null; @endphp
<div class="grid lg:grid-cols-[1fr_300px] gap-6 items-start">
    <div class="space-y-6">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título *</label>
                <input type="text" name="title" value="{{ old('title', $item?->title) }}" required maxlength="255" autofocus
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">URL (slug) *</label>
                <div class="flex items-center gap-1">
                    <span class="text-sm text-ink-400">/</span>
                    <input type="text" name="slug" value="{{ old('slug', $item?->slug) }}" required maxlength="255" placeholder="legal/privacidad"
                        class="flex-1 px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
                </div>
                @error('slug')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                <p class="text-xs text-ink-400 mt-1.5">Minúsculas, números, guiones y barras. Ej. <code>privacidad</code> o <code>legal/terminos</code>.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Contenido (admite HTML) *</label>
                <textarea name="description" rows="14" required
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono resize-y">{{ old('description', $item?->description) }}</textarea>
                @error('description')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
            <h3 class="font-display font-bold text-ink-900 text-sm flex items-center gap-2"><i class="fa-solid fa-magnifying-glass text-brand-600"></i> SEO (opcional)</h3>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Título SEO</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $item?->seo_title) }}" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Descripción SEO</label>
                <textarea name="seo_description" rows="2" maxlength="255"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y">{{ old('seo_description', $item?->seo_description) }}</textarea>
            </div>
        </section>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-24">
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 space-y-3">
            <h3 class="font-display font-bold text-ink-900 text-sm">Visibilidad</h3>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="status" value="1" @checked(old('status', $item?->status ?? true)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Publicada (visible)</span>
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="show_at_nav" value="1" @checked(old('show_at_nav', $item?->show_at_nav ?? false)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="text-ink-700">Mostrar en el menú</span>
            </label>
        </section>
    </aside>
</div>
