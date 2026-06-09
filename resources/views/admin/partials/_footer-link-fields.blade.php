@php $item = $item ?? null; @endphp
<section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Texto del enlace</label>
        <input type="text" name="title" value="{{ old('title', $item?->title) }}" required maxlength="120" autofocus
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">URL / ruta</label>
        <input type="text" name="url" value="{{ old('url', $item?->url) }}" required maxlength="255" placeholder="/courses o https://…"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
        @error('url')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="text-xs text-ink-400 mt-1.5">Puedes usar una ruta interna (ej. <code>/courses</code>) o una URL completa.</p>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Orden</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" required min="0" max="999"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('sort_order')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm cursor-pointer self-end pb-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
            <span class="text-ink-700">Visible en el pie de página</span>
        </label>
    </div>
</section>
