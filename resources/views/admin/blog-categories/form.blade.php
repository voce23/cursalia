@extends('layouts.admin')

@section('title', $category->exists ? 'Editar categoría' : 'Nueva categoría')
@section('page-title', $category->exists ? 'Editar categoría' : 'Nueva categoría')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.blog-categories.index') }}" class="hover:text-brand-700">Categorías</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $category->exists ? $category->name : 'Nueva' }}</span>
</nav>

<form method="POST" action="{{ $category->exists ? route('admin.blog-categories.update', $category) : route('admin.blog-categories.store') }}"
      class="max-w-xl bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-5">
    @csrf
    @if ($category->exists)@method('PUT')@endif

    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" required maxlength="120" autofocus
            class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        @if ($category->exists)
            <p class="text-xs text-ink-400 mt-2">Slug actual: <code class="px-2 py-0.5 rounded bg-cream-2">{{ $category->slug }}</code></p>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Color de identificación</label>
        <div class="flex items-center gap-3">
            <input type="color" name="color" value="{{ old('color', $category->color ?? '#10B981') }}"
                class="w-14 h-12 rounded-2xl border border-ink-200 cursor-pointer">
            <span class="text-sm font-mono text-ink-600">{{ old('color', $category->color ?? '#10B981') }}</span>
        </div>
        <p class="text-[10px] text-ink-400 mt-2">Aparece como acento del badge de categoría en el blog.</p>
    </div>

    <label class="flex items-center gap-2 text-sm cursor-pointer">
        <input type="checkbox" name="status" value="1" @checked(old('status', $category->status ?? true))
               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
        <span class="text-ink-700">Activa (visible en el blog público)</span>
    </label>

    <div class="flex gap-3 pt-4 border-t border-ink-200/70">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
            <i class="fa-solid fa-floppy-disk"></i> {{ $category->exists ? 'Guardar' : 'Crear' }}
        </button>
        <a href="{{ route('admin.blog-categories.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
    </div>
</form>
@endsection
