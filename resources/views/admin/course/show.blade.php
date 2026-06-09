@extends('layouts.admin')

@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', 'Detalle del curso')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700"><i class="fa-solid fa-arrow-left"></i> Cursos</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.courses.edit', $course) }}" class="px-4 py-2.5 rounded-full bg-white border border-ink-200 text-ink-700 text-sm font-bold hover:border-brand-300 transition"><i class="fa-solid fa-pen"></i> Editar</a>
        @if ($course->is_approved === 'approved')
            <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="px-4 py-2.5 rounded-full bg-white border border-ink-200 text-ink-700 text-sm font-bold hover:border-brand-300 transition"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver en la web</a>
        @endif
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6">
        @if ($course->thumbnail)
            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="" class="w-full h-56 rounded-2xl object-cover mb-5">
        @endif
        <h2 class="font-display font-extrabold text-2xl text-ink-900">{{ $course->title }}</h2>
        <p class="text-ink-600 mt-3 whitespace-pre-line">{{ $course->description }}</p>

        <h3 class="font-display font-bold text-ink-900 mt-8 mb-3">Contenido</h3>
        @forelse ($course->chapters as $chapter)
            <div class="mb-3">
                <p class="font-semibold text-ink-800 text-sm">{{ $loop->iteration }}. {{ $chapter->title }}</p>
                <ul class="mt-1 pl-5 text-sm text-ink-500 list-disc">
                    @foreach ($chapter->lessons as $lesson)
                        <li>{{ $lesson->title }}</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="text-sm text-ink-400">Este curso aún no tiene capítulos ni lecciones. (El constructor de contenido llega en la Fase 2.)</p>
        @endforelse
    </div>

    <div class="space-y-4">
        <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft p-6 text-sm space-y-3">
            <div class="flex justify-between"><span class="text-ink-400">Estado</span><span class="font-semibold">{{ ['approved'=>'Publicado','pending'=>'Pendiente','rejected'=>'Rechazado'][$course->is_approved] ?? $course->is_approved }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Instructor</span><span class="font-semibold">{{ $course->instructor?->name ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Categoría</span><span class="font-semibold">{{ $course->category?->name ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Nivel</span><span class="font-semibold">{{ $course->level?->name ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Idioma</span><span class="font-semibold">{{ $course->language?->name ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Duración</span><span class="font-semibold">{{ $course->duration ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-ink-400">Precio</span><span class="font-semibold">{{ $course->price > 0 ? number_format($course->price,2).' €' : 'Gratis' }}</span></div>
        </div>
    </div>
</div>

@endsection
