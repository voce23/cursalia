@php $item = $item ?? null; @endphp
<section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre de la red</label>
        <input type="text" name="name" value="{{ old('name', $item?->name) }}" required maxlength="80" autofocus placeholder="Instagram, YouTube, LinkedIn…"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Icono (Font Awesome)</label>
        <input type="text" name="icon_class" value="{{ old('icon_class', $item?->icon_class) }}" maxlength="120" placeholder="fa-brands fa-instagram"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
        @error('icon_class')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="text-xs text-ink-400 mt-1.5">Busca el icono en <a href="https://fontawesome.com/search?o=r&f=brands" target="_blank" class="text-brand-700 underline">fontawesome.com</a> (ej. <code>fa-brands fa-youtube</code>).</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">URL de tu perfil</label>
        <input type="url" name="url" value="{{ old('url', $item?->url) }}" required maxlength="255" placeholder="https://instagram.com/tucuenta"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
        @error('url')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
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
