@extends('layouts.dashboard')

@section('title', 'Mi panel')
@section('page-title', 'Mi panel')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Saludo --}}
    <div class="bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-3xl p-7 sm:p-9 shadow-lift relative overflow-hidden">
        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
        <div class="relative">
            <p class="text-brand-100 text-sm font-medium">¡Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</p>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-1">
                Bienvenido a tu academia
            </h2>
            <p class="text-brand-50/90 mt-3 max-w-lg text-sm sm:text-base">
                Aquí verás tus cursos, tu progreso y tus certificados. Sigue aprendiendo a tu ritmo.
            </p>
            @if ($enrolledCourses === 0)
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-3 rounded-2xl bg-white text-ink-900 font-bold shadow-soft hover:scale-[1.02] transition">
                    Explorar cursos <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        @php
            $stats = [
                ['Cursos inscritos', $enrolledCourses,      'fa-book-open',     'brand'],
                ['Progreso medio',   $averageProgress.'%',  'fa-chart-line',    'sun'],
                ['Reseñas escritas', $totalReviews,         'fa-star',          'coral'],
                ['Compras',          $totalOrders,          'fa-bag-shopping', 'brand'],
            ];
        @endphp
        @foreach ($stats as [$lbl, $val, $ic, $color])
            <div class="card-lift bg-white border border-ink-200/70 rounded-3xl p-5 shadow-soft">
                <span class="grid place-items-center w-11 h-11 rounded-2xl
                    @if($color === 'brand') bg-brand-100 text-brand-600
                    @elseif($color === 'coral') bg-coral-100 text-coral-500
                    @else bg-sun-100 text-sun-500 @endif">
                    <i class="fa-solid {{ $ic }}"></i>
                </span>
                <p class="text-2xl font-display font-extrabold text-ink-900 mt-4">{{ $val }}</p>
                <p class="text-sm text-ink-500 mt-1">{{ $lbl }}</p>
            </div>
        @endforeach
    </div>

    {{-- Cursos recientes --}}
    <section class="mt-10">
        <div class="flex items-end justify-between gap-3 mb-5">
            <div>
                <h3 class="font-display font-extrabold text-xl text-ink-900">Sigue aprendiendo</h3>
                <p class="text-sm text-ink-500 mt-1">Tus cursos más recientes</p>
            </div>
        </div>

        @if ($recentCourses->isEmpty())
            <div class="rounded-3xl bg-white border-2 border-dashed border-ink-200 p-10 text-center">
                <span class="grid place-items-center w-14 h-14 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
                    <i class="fa-regular fa-folder-open text-xl"></i>
                </span>
                <p class="font-display font-bold text-ink-900 mt-4">Aún no estás inscrito en ningún curso</p>
                <p class="text-sm text-ink-500 mt-1">Explora el catálogo y empieza tu primer curso gratis.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($recentCourses as $enrollment)
                    @php
                        $course = $enrollment->course;
                        if (!$course) continue;
                        $totalLessons = $course->lessons_count ?? 0;
                        $completed = $completedByCourse->get($course->id)?->completed_count ?? 0;
                        $pct = $totalLessons > 0 ? (int) round(($completed / $totalLessons) * 100) : 0;
                    @endphp
                    <div class="card-lift bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden">
                        <div class="relative h-32 bg-gradient-to-br from-brand-300 to-brand-500">
                            @if ($course->thumbnail)
                                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="absolute inset-0 w-full h-full object-cover">
                            @endif
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 text-brand-700 text-[11px] font-bold">
                                {{ $pct }}% completado
                            </span>
                        </div>
                        <div class="p-5">
                            <h4 class="font-display font-bold text-ink-900 leading-snug line-clamp-2">{{ $course->title }}</h4>
                            <p class="text-xs text-ink-500 mt-2">{{ $course->instructor?->name ?? 'Instructor' }} · {{ $totalLessons }} lecciones</p>
                            <div class="mt-4 h-2 rounded-full bg-cream-2 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-400 to-brand-600" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <p class="text-center text-xs text-ink-400 mt-12">
        <i class="fa-solid fa-flask text-brand-500"></i> Sprint 2 — Auth + estudiante listo · Próximo: rediseñar el frontend público
    </p>
</div>
@endsection
