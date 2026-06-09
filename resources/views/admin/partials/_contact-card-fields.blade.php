@php $item = $item ?? null; @endphp
<section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Título</label>
        <input type="text" name="title" value="{{ old('title', $item?->title) }}" required maxlength="120" autofocus placeholder="Escríbenos, Llámanos, Visítanos…"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        @error('title')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Icono (Font Awesome)</label>
        <input type="text" name="icon" value="{{ old('icon', $item?->icon) }}" maxlength="120" placeholder="fa-solid fa-envelope"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm font-mono">
        @error('icon')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="text-xs text-ink-400 mt-1.5">Ej. <code>fa-solid fa-envelope</code>, <code>fa-solid fa-phone</code>, <code>fa-solid fa-location-dot</code>.</p>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Línea 1</label>
            <input type="text" name="line_one" value="{{ old('line_one', $item?->line_one) }}" maxlength="255" placeholder="hola@tusitio.com"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('line_one')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Línea 2</label>
            <input type="text" name="line_two" value="{{ old('line_two', $item?->line_two) }}" maxlength="255" placeholder="Respondemos en 24 h"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('line_two')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
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
            <span class="text-ink-700">Visible en la página de contacto</span>
        </label>
    </div>
</section>
