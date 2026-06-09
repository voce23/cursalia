@extends('layouts.admin')

@section('title', 'Contenido · '.$course->title)
@section('page-title', 'Contenido del curso')
@section('page-subtitle', $course->title)

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700"><i class="fa-solid fa-arrow-left"></i> Cursos</a>
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.courses.edit', $course) }}" class="px-4 py-2.5 rounded-full bg-white border border-ink-200 text-ink-700 font-bold hover:border-brand-300 transition"><i class="fa-solid fa-sliders"></i> Datos del curso</a>
        @if ($course->is_approved === 'approved')
            <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="px-4 py-2.5 rounded-full bg-white border border-ink-200 text-ink-700 font-bold hover:border-brand-300 transition"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver</a>
        @endif
    </div>
</div>

<p class="text-sm text-ink-500 mb-5">{{ $course->chapters->count() }} capítulos · {{ $course->chapters->sum(fn ($c) => $c->lessons->count()) }} lecciones</p>

{{-- Capítulos --}}
<div class="space-y-5">
    @forelse ($course->chapters as $chapter)
        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden" x-data="{ rename: false, addLesson: false }">
            {{-- Cabecera del capítulo --}}
            <div class="flex items-center gap-3 px-5 py-3.5 bg-cream-2/60 border-b border-ink-100">
                <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-100 text-brand-700 text-xs font-bold">{{ $loop->iteration }}</span>
                <div class="flex-1 min-w-0">
                    <p x-show="!rename" class="font-display font-bold text-ink-900 truncate">{{ $chapter->title }}
                        <span class="text-xs font-normal text-ink-400">· {{ $chapter->lessons->count() }} lecciones</span>
                    </p>
                    <form x-show="rename" x-cloak method="POST" action="{{ route('admin.chapters.update', $chapter) }}" class="flex items-center gap-2">
                        @csrf @method('PUT')
                        <input type="text" name="title" value="{{ $chapter->title }}" maxlength="255" class="flex-1 rounded-lg border border-ink-200 px-3 py-1.5 text-sm">
                        <button class="px-3 py-1.5 rounded-lg bg-brand-600 text-white text-xs font-bold">Guardar</button>
                        <button type="button" @click="rename=false" class="text-ink-400 text-xs">Cancelar</button>
                    </form>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button @click="rename=!rename" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Renombrar"><i class="fa-solid fa-pen text-xs"></i></button>
                    <form method="POST" action="{{ route('admin.chapters.move', [$chapter, 'up']) }}">@csrf<button class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Subir"><i class="fa-solid fa-chevron-up text-xs"></i></button></form>
                    <form method="POST" action="{{ route('admin.chapters.move', [$chapter, 'down']) }}">@csrf<button class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Bajar"><i class="fa-solid fa-chevron-down text-xs"></i></button></form>
                    <form method="POST" action="{{ route('admin.chapters.destroy', $chapter) }}" onsubmit="return confirm('¿Eliminar el capítulo y todas sus lecciones?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                </div>
            </div>

            {{-- Lecciones --}}
            <ul class="divide-y divide-ink-100">
                @forelse ($chapter->lessons as $lesson)
                    <li class="px-5 py-3" x-data="{ edit: false, storage: '{{ $lesson->storage }}' }">
                        <div class="flex items-center gap-3">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-ink-100 text-ink-500 text-xs font-bold shrink-0">{{ $lesson->order }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-ink-800 truncate">{{ $lesson->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] font-bold bg-ink-100 text-ink-500 rounded px-1.5 py-0.5 uppercase">{{ $lesson->storage }}</span>
                                    @if ($lesson->duration)<span class="text-[10px] text-ink-400">{{ $lesson->duration }}</span>@endif
                                    @if ($lesson->is_preview)<span class="text-[10px] font-bold bg-brand-50 text-brand-700 rounded px-1.5 py-0.5">Preview</span>@endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <form method="POST" action="{{ route('admin.lessons.move', [$lesson, 'up']) }}">@csrf<button class="grid place-items-center w-7 h-7 rounded-lg text-ink-400 hover:bg-ink-100" title="Subir"><i class="fa-solid fa-chevron-up text-[10px]"></i></button></form>
                                <form method="POST" action="{{ route('admin.lessons.move', [$lesson, 'down']) }}">@csrf<button class="grid place-items-center w-7 h-7 rounded-lg text-ink-400 hover:bg-ink-100" title="Bajar"><i class="fa-solid fa-chevron-down text-[10px]"></i></button></form>
                                <button @click="edit=!edit" class="grid place-items-center w-7 h-7 rounded-lg text-ink-500 hover:bg-ink-100" title="Editar"><i class="fa-solid fa-pen text-[10px]"></i></button>
                                <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('¿Eliminar esta lección?')">@csrf @method('DELETE')<button class="grid place-items-center w-7 h-7 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-[10px]"></i></button></form>
                            </div>
                        </div>
                        {{-- Form editar lección --}}
                        <form x-show="edit" x-cloak method="POST" action="{{ route('admin.lessons.update', $lesson) }}" enctype="multipart/form-data" class="mt-3 p-4 rounded-xl bg-cream-2/50 border border-ink-100">
                            @csrf @method('PUT')
                            @include('admin.course._lesson_fields', ['lesson' => $lesson])
                            <div class="mt-3 flex items-center gap-2">
                                <button class="px-4 py-2 rounded-lg bg-brand-600 text-white text-xs font-bold">Guardar lección</button>
                                <button type="button" @click="edit=false" class="text-ink-400 text-xs">Cancelar</button>
                            </div>
                        </form>
                    </li>
                @empty
                    <li class="px-5 py-3 text-sm text-ink-400">Este capítulo aún no tiene lecciones.</li>
                @endforelse
            </ul>

            {{-- Añadir lección --}}
            <div class="px-5 py-3 border-t border-ink-100 bg-white">
                <button @click="addLesson=!addLesson" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-600 hover:text-brand-700">
                    <i class="fa-solid fa-plus"></i> Añadir lección
                </button>
                <form x-show="addLesson" x-cloak method="POST" action="{{ route('admin.lessons.store', $chapter) }}" enctype="multipart/form-data"
                      class="mt-3 p-4 rounded-xl bg-cream-2/50 border border-ink-100" x-data="{ storage: 'youtube' }">
                    @csrf
                    @include('admin.course._lesson_fields', ['lesson' => null])
                    <div class="mt-3 flex items-center gap-2">
                        <button class="px-4 py-2 rounded-lg bg-brand-600 text-white text-xs font-bold">Añadir lección</button>
                        <button type="button" @click="addLesson=false" class="text-ink-400 text-xs">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl bg-white border border-dashed border-ink-200 p-10 text-center text-ink-400">
            Este curso todavía no tiene capítulos. Crea el primero abajo.
        </div>
    @endforelse
</div>

{{-- Añadir capítulo --}}
<form method="POST" action="{{ route('admin.chapters.store', $course) }}" class="mt-6 flex items-center gap-3">
    @csrf
    <input type="text" name="title" required maxlength="255" placeholder="Nombre del nuevo capítulo (ej. Introducción)"
           class="flex-1 rounded-xl border border-ink-200 px-4 py-2.5 text-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
    <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-ink-900 text-white text-sm font-bold hover:bg-ink-800 transition"><i class="fa-solid fa-plus"></i> Añadir capítulo</button>
</form>

@endsection
