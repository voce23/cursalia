@extends('layouts.dashboard')

@section('title', 'Mis cursos')
@section('page-title', 'Mis cursos')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Encabezado --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-ink-900">
                Sigue aprendiendo 📚
            </h2>
            <p class="mt-1 text-ink-500">Retoma donde lo dejaste o empieza algo nuevo.</p>
        </div>
        <a href="{{ route('courses.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition shadow-soft">
            <i class="fa-solid fa-magnifying-glass"></i> Explorar más cursos
        </a>
    </div>

    @if ($enrolledCourses->isEmpty())
        {{-- Estado vacío --}}
        <div class="bg-white border border-ink-200/70 rounded-3xl p-10 sm:p-14 text-center shadow-soft">
            <span class="grid place-items-center w-16 h-16 mx-auto rounded-2xl bg-brand-50 text-brand-500 text-2xl">
                <i class="fa-solid fa-graduation-cap"></i>
            </span>
            <h3 class="mt-5 font-display font-bold text-xl text-ink-900">Todavía no tienes cursos</h3>
            <p class="mt-2 text-ink-500 max-w-md mx-auto">
                Cuando te inscribas en un curso gratuito aparecerá aquí con tu progreso.
            </p>
            <a href="{{ route('courses.index') }}"
               class="mt-6 inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition shadow-soft">
                Ver cursos disponibles <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($enrolledCourses as $enrollment)
                @php
                    $course = $enrollment->course;
                    if (! $course) continue;
                    $total       = $course->lessons_count ?? 0;
                    $completed   = optional($completions->get($course->id))->completed_count ?? 0;
                    $progress    = $total > 0 ? (int) round($completed / $total * 100) : 0;
                    $history     = $watchHistories->get($course->id);
                    $resumeUrl   = route('student.player.show', $course)
                                    . ($history ? '?lesson=' . $history->lesson_id : '');
                    $isDone      = $progress >= 100;
                @endphp

                <article class="group bg-white border border-ink-200/70 rounded-3xl overflow-hidden shadow-soft hover:shadow-lift transition flex flex-col">
                    {{-- Thumb --}}
                    <a href="{{ $resumeUrl }}" class="block relative aspect-video bg-gradient-to-br from-brand-400 to-brand-600 overflow-hidden">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                 loading="lazy" decoding="async">
                        @else
                            <span class="absolute inset-0 grid place-items-center text-white/90 text-4xl font-display font-extrabold">
                                {{ strtoupper(substr($course->title, 0, 2)) }}
                            </span>
                        @endif
                        @if ($isDone)
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 text-brand-700 text-xs font-bold shadow">
                                <i class="fa-solid fa-circle-check"></i> Completado
                            </span>
                        @endif
                    </a>

                    {{-- Cuerpo --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display font-bold text-ink-900 leading-snug line-clamp-2">
                            <a href="{{ $resumeUrl }}" class="hover:text-brand-600 transition">{{ $course->title }}</a>
                        </h3>
                        @if ($course->instructor)
                            <p class="mt-1 text-xs text-ink-500">
                                <i class="fa-solid fa-chalkboard-user mr-1"></i>{{ $course->instructor->name }}
                            </p>
                        @endif

                        {{-- Progreso --}}
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-ink-500 mb-1.5">
                                <span>{{ $completed }}/{{ $total }} lecciones</span>
                                <span class="font-semibold text-brand-600">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-cream-2 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all duration-500"
                                     style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <a href="{{ $resumeUrl }}"
                           class="mt-5 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-sm font-semibold transition
                                  {{ $isDone
                                      ? 'bg-cream-2 text-ink-700 hover:bg-ink-100'
                                      : 'bg-brand-500 hover:bg-brand-600 text-white shadow-soft' }}">
                            @if ($isDone)
                                <i class="fa-solid fa-rotate-right"></i> Repasar
                            @elseif ($completed > 0)
                                <i class="fa-solid fa-play"></i> Continuar
                            @else
                                <i class="fa-solid fa-play"></i> Empezar
                            @endif
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $enrolledCourses->links() }}
        </div>
    @endif

</div>
@endsection
