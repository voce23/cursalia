@extends('layouts.admin')

@section('title', 'Editar categoría')
@section('page-title', 'Editar categoría')
@section('page-subtitle', $courseCategory->name)

@section('content')

<div class="max-w-2xl">
    <nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
        <a href="{{ route('admin.course-categories.index') }}" class="hover:text-brand-700">Categorías</a>
        <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
        <span class="text-ink-900 font-medium">{{ $courseCategory->name }}</span>
    </nav>

    <form method="POST" action="{{ route('admin.course-categories.update', $courseCategory) }}" enctype="multipart/form-data"
          class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-6"
          x-data="{ preview: null }">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5" for="name">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name', $courseCategory->name) }}" required maxlength="255" autofocus
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400">
            @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            <p class="text-xs text-ink-400 mt-2">Slug actual: <code class="px-2 py-0.5 rounded bg-cream-2">{{ $courseCategory->slug }}</code></p>
        </div>

        {{-- Imagen actual + reemplazar --}}
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Imagen</label>

            @if ($courseCategory->image)
                <div class="rounded-2xl overflow-hidden border border-ink-200/70 mb-3">
                    <img src="{{ asset('storage/'.$courseCategory->image) }}" alt="{{ $courseCategory->name }}" class="w-full h-48 object-cover">
                </div>
            @endif

            <label for="image" class="block">
                <div class="relative rounded-2xl border-2 border-dashed border-ink-200 bg-cream-2 hover:bg-brand-50 hover:border-brand-300 transition cursor-pointer overflow-hidden">
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-48 object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="py-8 text-center">
                            <span class="grid place-items-center w-10 h-10 rounded-2xl bg-white text-brand-600 mx-auto shadow-soft">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </span>
                            <p class="mt-2 font-semibold text-ink-700 text-sm">{{ $courseCategory->image ? 'Reemplazar imagen' : 'Subir imagen' }}</p>
                            <p class="text-xs text-ink-500 mt-1">JPG, PNG o WEBP · Máx 3 MB</p>
                        </div>
                    </template>
                </div>
            </label>
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only"
                @change="
                    const f = $event.target.files[0];
                    if (f) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); }
                ">
            @error('image')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>

        {{-- Acciones --}}
        <div class="flex flex-wrap gap-3 pt-4 border-t border-ink-200/70">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
            <a href="{{ route('admin.course-categories.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold border border-ink-200 hover:bg-cream-2 text-ink-700 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

@endsection
