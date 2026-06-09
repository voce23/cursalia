@extends('layouts.admin')

@section('title', 'Cabecera')
@section('page-title', 'Cabecera del sitio')
@section('page-subtitle', 'Menú de categorías y buscador de la barra superior')

@section('content')

<form method="POST" action="{{ route('admin.header-settings.update') }}" class="max-w-2xl space-y-6">
    @csrf

    <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
        <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2"><i class="fa-solid fa-folder-tree text-brand-600"></i> Menú de categorías</h2>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Texto del botón *</label>
            <input type="text" name="category_button_text" value="{{ old('category_button_text', $setting->category_button_text) }}" required maxlength="80"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('category_button_text')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Nº de categorías en el menú *</label>
            <input type="number" name="category_limit" value="{{ old('category_limit', $setting->category_limit) }}" required min="1" max="20"
                class="w-32 px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('category_limit')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
    </section>

    <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
        <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2"><i class="fa-solid fa-magnifying-glass text-coral-500"></i> Buscador</h2>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="show_search" value="1" @checked(old('show_search', $setting->show_search)) class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
            <span class="text-ink-700">Mostrar el buscador en la cabecera</span>
        </label>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Texto de ejemplo del buscador *</label>
            <input type="text" name="search_placeholder" value="{{ old('search_placeholder', $setting->search_placeholder) }}" required maxlength="120"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('search_placeholder')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
    </section>

    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
        <p class="text-xs text-ink-400">El logo y el menú principal se editan en <a href="{{ route('admin.appearance.edit') }}" class="text-brand-700 underline">Apariencia</a> y <a href="{{ route('admin.navigation.edit') }}" class="text-brand-700 underline">Navegación</a>.</p>
    </div>
</form>

@endsection
