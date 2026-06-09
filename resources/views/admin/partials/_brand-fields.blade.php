@php $item = $item ?? null; @endphp
<section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre de la marca *</label>
        <input type="text" name="name" value="{{ old('name', $item?->name) }}" required maxlength="120" autofocus
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Enlace (opcional)</label>
        <input type="text" name="url" value="{{ old('url', $item?->url) }}" maxlength="255" placeholder="https://…"
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
            <span class="text-ink-700">Visible en el inicio</span>
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Logo</label>
        <div class="flex items-center gap-4">
            @if ($item?->logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->logo) }}" alt="" class="h-12 max-w-[120px] object-contain border border-ink-200 rounded-lg bg-white p-1">
            @endif
            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                class="flex-1 text-sm text-ink-600 file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
        </div>
        @error('logo')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="text-xs text-ink-400 mt-1.5">PNG, JPG, WebP o SVG, máx. 2 MB.</p>
    </div>
</section>
