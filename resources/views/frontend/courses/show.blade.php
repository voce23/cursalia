@extends('layouts.app')

@section('title', $course->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($course->seo_description ?: $course->description ?? $course->title), 155))

@section('content')

@php
    $isFree = (float) $course->price === 0.0;
    $hasDiscount = ! $isFree && $course->discount > 0;
@endphp

{{-- ═══════════════════════════════════════════════════════════════════
     HERO DEL CURSO (split: info + tarjeta de compra/inscripción)
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-ink-950 text-white pt-14 pb-32 overflow-hidden">
    <div class="blob bg-brand-600/30 w-[28rem] h-[28rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-500/25 w-[22rem] h-[22rem] top-20 right-0"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-white/60 mb-6">
            <a href="{{ url('/') }}" class="hover:text-white">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px]"></i>
            <a href="{{ route('courses.index') }}" class="hover:text-white">Cursos</a>
            @if ($course->category)
                <i class="fa-solid fa-angle-right text-[10px]"></i>
                <a href="{{ route('courses.index', ['category' => $course->category->slug]) }}" class="hover:text-white">{{ $course->category->name }}</a>
            @endif
        </div>

        <div class="grid lg:grid-cols-[1.4fr_1fr] gap-10 lg:gap-14 items-start">

            {{-- Columna izquierda: info --}}
            <div class="max-w-2xl">
                @if ($course->category)
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold text-brand-300">
                        <i class="fa-solid fa-tag text-[10px]"></i> {{ $course->category->name }}
                    </span>
                @endif

                <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-[1.1] mt-5">{{ $course->title }}</h1>

                @if ($course->seo_description)
                    <p class="text-white/75 text-lg leading-relaxed mt-5">{{ $course->seo_description }}</p>
                @endif

                {{-- Meta info --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3 mt-7 text-sm">
                    @if ($course->reviews_avg_rating)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="text-sun-400">{{ str_repeat('★', (int) round($course->reviews_avg_rating)) }}</span>
                            <span class="font-bold">{{ number_format($course->reviews_avg_rating, 1) }}</span>
                            <span class="text-white/60">({{ $reviews->total() }} reseñas)</span>
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-regular fa-circle-play text-brand-300"></i> {{ $course->lessons_count }} lecciones</span>
                    @if ($course->duration)
                        <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-regular fa-clock text-brand-300"></i> {{ $course->duration }}</span>
                    @endif
                    @if ($course->level)
                        <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-solid fa-bars-staggered text-brand-300"></i> {{ $course->level->name }}</span>
                    @endif
                    @if ($course->language)
                        <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-solid fa-language text-brand-300"></i> {{ $course->language->name }}</span>
                    @endif
                </div>

                {{-- Instructor --}}
                @if ($course->instructor)
                    <div class="mt-8 flex items-center gap-3">
                        @if ($course->instructor->image)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.$course->instructor->image) }}" alt="{{ $course->instructor->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/20">
                        @else
                            <span class="grid place-items-center w-12 h-12 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-display font-bold">
                                {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                            </span>
                        @endif
                        <div>
                            <p class="text-xs text-white/60">Tu instructor</p>
                            <p class="font-display font-bold">{{ $course->instructor->name }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Columna derecha: tarjeta de compra/inscripción --}}
            <aside class="lg:sticky lg:top-24">
                <div class="bg-white text-ink-900 rounded-3xl shadow-lift border border-ink-200/70 overflow-hidden">
                    {{-- Thumbnail / preview --}}
                    <div class="relative aspect-video bg-gradient-to-br from-brand-300 to-brand-600">
                        @if ($course->thumbnail)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="absolute inset-0 w-full h-full object-cover">
                        @endif
                        @if ($course->demo_video_storage)
                            <span class="absolute inset-0 grid place-items-center bg-ink-950/30">
                                <span class="grid place-items-center w-16 h-16 rounded-full bg-white text-brand-700 shadow-lift">
                                    <i class="fa-solid fa-play text-xl ml-1"></i>
                                </span>
                            </span>
                            <span class="absolute bottom-3 left-3 px-3 py-1 rounded-full bg-ink-950/70 backdrop-blur text-white text-[11px] font-semibold">Vista previa</span>
                        @endif
                    </div>

                    <div class="p-6">
                        {{-- Precio --}}
                        <div class="flex items-baseline gap-3">
                            @if ($isFree)
                                <span class="font-display font-extrabold text-3xl text-brand-600">Gratis</span>
                                <span class="text-xs text-ink-500">para siempre</span>
                            @elseif ($hasDiscount)
                                <span class="font-display font-extrabold text-3xl text-ink-900">${{ number_format($course->discount, 0) }}</span>
                                <span class="text-base text-ink-400 line-through">${{ number_format($course->price, 0) }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-coral-100 text-coral-600 text-[10px] font-bold uppercase">
                                    -{{ (int) round((1 - $course->discount / $course->price) * 100) }}%
                                </span>
                            @else
                                <span class="font-display font-extrabold text-3xl text-ink-900">${{ number_format($course->price, 0) }}</span>
                            @endif
                        </div>

                        {{-- Flash --}}
                        @if (session('status'))
                            <p class="mt-4 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm">
                                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                            </p>
                        @endif

                        {{-- CTA principal --}}
                        <div class="mt-5">
                            @if ($isEnrolled)
                                <a href="{{ route('student.player.show', $course) }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                                    <i class="fa-solid fa-circle-play"></i> Ir al curso
                                </a>
                                <p class="text-center text-xs text-brand-600 mt-2"><i class="fa-solid fa-check"></i> Ya estás inscrito</p>
                            @elseif ($isFree)
                                @auth
                                    <form method="POST" action="{{ route('courses.enroll-free', $course->slug) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                                            <i class="fa-solid fa-gift"></i> Inscribirme gratis
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                                        Inicia sesión para inscribirte <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                @endauth
                            @else
                                {{-- Cursos de pago — FASE 2 --}}
                                <button disabled class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-ink-100 text-ink-400 cursor-not-allowed">
                                    <i class="fa-solid fa-lock"></i> Próximamente con pagos
                                </button>
                                <p class="text-center text-xs text-ink-400 mt-2">Los cursos de pago llegan en la siguiente fase.</p>
                            @endif
                        </div>

                        {{-- Lo que incluye --}}
                        <div class="mt-7 pt-5 border-t border-ink-200/70">
                            <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Incluye</p>
                            <ul class="space-y-2.5 text-sm text-ink-700">
                                <li class="flex items-start gap-2.5">
                                    <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 shrink-0 text-[10px]"><i class="fa-solid fa-check"></i></span>
                                    {{ $course->lessons_count }} lecciones a tu ritmo
                                </li>
                                @if ($course->duration)
                                    <li class="flex items-start gap-2.5">
                                        <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 shrink-0 text-[10px]"><i class="fa-solid fa-check"></i></span>
                                        {{ $course->duration }} de contenido
                                    </li>
                                @endif
                                <li class="flex items-start gap-2.5">
                                    <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 shrink-0 text-[10px]"><i class="fa-solid fa-check"></i></span>
                                    Acceso desde cualquier dispositivo
                                </li>
                                @if ($course->certificate)
                                    <li class="flex items-start gap-2.5">
                                        <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 shrink-0 text-[10px]"><i class="fa-solid fa-check"></i></span>
                                        Certificado al completar
                                    </li>
                                @endif
                                @if ($course->qna)
                                    <li class="flex items-start gap-2.5">
                                        <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 shrink-0 text-[10px]"><i class="fa-solid fa-check"></i></span>
                                        Foro de preguntas y respuestas
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <p class="text-center text-xs text-white/60 mt-4">
                    <i class="fa-solid fa-shield-halved text-brand-300"></i> Compra segura y datos protegidos
                </p>
            </aside>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     CUERPO: descripción + plan de estudios + reseñas
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-10">
    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-10 lg:gap-14">

        <div class="space-y-10">

            {{-- Descripción --}}
            @if ($course->description)
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 sm:p-8">
                    <h2 class="font-display font-extrabold text-2xl text-ink-900 flex items-center gap-2">
                        <i class="fa-solid fa-book-open text-brand-600 text-base"></i> Sobre el curso
                    </h2>
                    <div class="prose prose-sm sm:prose-base max-w-none mt-5 text-ink-700">
                        {!! Purifier::clean($course->description, 'richtext') !!}
                    </div>
                </div>
            @endif

            {{-- Plan de estudios --}}
            @if ($course->chapters->isNotEmpty())
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 sm:p-8">
                    <div class="flex items-end justify-between gap-3 mb-6">
                        <h2 class="font-display font-extrabold text-2xl text-ink-900 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-brand-600 text-base"></i> Plan de estudios
                        </h2>
                        <p class="text-sm text-ink-500">
                            <b class="text-ink-900">{{ $course->chapters->count() }}</b> capítulos ·
                            <b class="text-ink-900">{{ $course->lessons_count }}</b> lecciones
                        </p>
                    </div>

                    <div class="space-y-3" x-data="{ open: 0 }">
                        @foreach ($course->chapters->sortBy('order') as $i => $chapter)
                            <div class="rounded-2xl border border-ink-200/70 overflow-hidden">
                                <button type="button" @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
                                        class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-cream-2 transition text-left">
                                    <span class="grid place-items-center w-8 h-8 rounded-xl bg-brand-100 text-brand-700 font-display font-bold text-sm">
                                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="flex-1">
                                        <span class="block font-display font-bold text-ink-900">{{ $chapter->title }}</span>
                                        <span class="block text-xs text-ink-500 mt-0.5">{{ $chapter->lessons->count() }} lecciones</span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-ink-400 transition" :class="open === {{ $i }} && 'rotate-180 text-brand-600'"></i>
                                </button>

                                <div x-show="open === {{ $i }}" x-collapse>
                                    <ul class="px-4 pb-4 space-y-1.5 border-t border-ink-200/70 pt-3">
                                        @foreach ($chapter->lessons->sortBy('order') as $lesson)
                                            <li class="flex items-center gap-3 text-sm px-2 py-2 rounded-xl hover:bg-cream-2 transition">
                                                <span class="grid place-items-center w-6 h-6 rounded-full bg-brand-50 text-brand-600 text-[10px] shrink-0">
                                                    @if ($lesson->file_type === 'video')
                                                        <i class="fa-solid fa-play"></i>
                                                    @else
                                                        <i class="fa-solid fa-file-lines"></i>
                                                    @endif
                                                </span>
                                                <span class="flex-1 text-ink-700">{{ $lesson->title }}</span>
                                                @if ($lesson->is_preview)
                                                    <span class="px-2 py-0.5 rounded-full bg-coral-100 text-coral-600 text-[10px] font-bold">Vista previa</span>
                                                @endif
                                                @if ($lesson->duration)
                                                    <span class="text-xs text-ink-400">{{ $lesson->duration }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reseñas --}}
            @if ($reviews->isNotEmpty())
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <h2 class="font-display font-extrabold text-2xl text-ink-900 flex items-center gap-2">
                            <i class="fa-solid fa-star text-sun-500 text-base"></i> Lo que dicen
                        </h2>
                        @if ($course->reviews_avg_rating)
                            <span class="font-display font-extrabold text-lg text-ink-900">
                                <span class="text-sun-500">★</span> {{ number_format($course->reviews_avg_rating, 1) }} <span class="text-ink-400 text-sm font-medium">({{ $reviews->total() }})</span>
                            </span>
                        @endif
                    </div>

                    <div class="space-y-5">
                        @foreach ($reviews as $r)
                            <article class="flex gap-4 pb-5 border-b border-ink-200/60 last:border-0 last:pb-0">
                                @if ($r->user?->image)
                                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$r->user->image) }}" alt="{{ $r->user->name }}" class="w-11 h-11 rounded-full object-cover shrink-0">
                                @else
                                    <span class="grid place-items-center w-11 h-11 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold shrink-0">
                                        {{ strtoupper(substr($r->user?->name ?? '?', 0, 1)) }}
                                    </span>
                                @endif
                                <div>
                                    <p class="font-display font-bold text-ink-900">{{ $r->user?->name ?? 'Estudiante' }}</p>
                                    <p class="text-sun-500 text-xs mt-0.5">{{ str_repeat('★', (int) $r->rating) }}<span class="text-ink-300">{{ str_repeat('★', 5 - (int) $r->rating) }}</span></p>
                                    <p class="text-sm text-ink-700 mt-2 leading-relaxed">{{ $r->review }}</p>
                                    <p class="text-xs text-ink-400 mt-2">{{ $r->created_at?->diffForHumans() }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($reviews->hasPages())
                        <div class="mt-6">{{ $reviews->links() }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Aside: instructor + cursos relacionados (placeholder) --}}
        <aside class="space-y-6 lg:pt-2">
            @if ($course->instructor)
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">Tu instructor</p>
                    <div class="flex items-center gap-3 mt-3">
                        @if ($course->instructor->image)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.$course->instructor->image) }}" alt="{{ $course->instructor->name }}" class="w-14 h-14 rounded-full object-cover">
                        @else
                            <span class="grid place-items-center w-14 h-14 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-display font-bold text-lg shrink-0">
                                {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <p class="font-display font-bold text-ink-900 truncate">{{ $course->instructor->name }}</p>
                            <p class="text-xs text-ink-500 truncate">{{ $course->instructor->headline ?? 'Instructor en Cursalia' }}</p>
                        </div>
                    </div>
                    @if ($course->instructor->bio)
                        <p class="text-sm text-ink-700 mt-4 leading-relaxed line-clamp-4">{{ strip_tags($course->instructor->bio) }}</p>
                    @endif
                </div>
            @endif

            <div class="bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-3xl p-6 shadow-lift relative overflow-hidden">
                <div class="blob bg-sun-300/40 w-44 h-44 -top-10 -right-10"></div>
                <div class="relative">
                    <i class="fa-solid fa-lightbulb text-sun-300 text-xl"></i>
                    <h3 class="font-display font-extrabold text-xl mt-3">¿Te quedaste con dudas?</h3>
                    <p class="text-brand-50/90 text-sm mt-2">Escríbenos y te ayudamos a elegir el curso ideal para tu meta.</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 rounded-full bg-white text-ink-900 font-bold text-sm hover:scale-[1.02] transition">
                        Contactar <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </aside>

    </div>
</section>

@endsection
