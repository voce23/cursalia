@php $item = $item ?? null; @endphp
<section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $item?->name) }}" required maxlength="120" autofocus
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Cargo / rol (opcional)</label>
            <input type="text" name="designation" value="{{ old('designation', $item?->designation) }}" maxlength="120" placeholder="Estudiante, Diseñadora…"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('designation')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Testimonio</label>
        <textarea name="message" rows="4" required maxlength="2000"
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y">{{ old('message', $item?->message) }}</textarea>
        @error('message')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
    </div>
    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Valoración</label>
            <select name="rating" class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(old('rating', $item?->rating ?? 5) == $i)>{{ str_repeat('★', $i) }} ({{ $i }})</option>
                @endfor
            </select>
            @error('rating')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Orden</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" required min="0" max="999"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('sort_order')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm cursor-pointer self-end pb-3">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item?->is_active ?? true)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
            <span class="text-ink-700">Visible en el sitio</span>
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Foto (opcional)</label>
        <div class="flex items-center gap-4">
            @if ($item?->avatar)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->avatar) }}" alt="" class="w-14 h-14 rounded-full object-cover border border-ink-200">
            @endif
            <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                class="flex-1 text-sm text-ink-600 file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
        </div>
        @error('avatar')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="text-xs text-ink-400 mt-1.5">PNG, JPG o WebP, máx. 2 MB. Si no subes nada, se muestra la inicial del nombre.</p>
    </div>
</section>
