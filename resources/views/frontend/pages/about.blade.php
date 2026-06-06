@extends('layouts.app')

@section('title', $about?->title ?: 'Sobre Cursalia')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO Nosotros — claro, cálido, editorial
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[28rem] h-[28rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[22rem] h-[22rem] top-32 -right-10"></div>
    <div class="blob bg-sun-200 w-[18rem] h-[18rem] top-60 left-1/3"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 text-center">
        <div class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <i class="fa-solid fa-heart text-coral-500"></i> Sobre Cursalia
        </div>
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-6 text-ink-900">
            {{ $about?->title ?: 'Hacemos del aprendizaje algo' }} <span class="text-brand-600">posible.</span>
        </h1>
        @if ($about?->subtitle)
            <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-6 max-w-2xl mx-auto">{{ $about->subtitle }}</p>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     CONTENIDO ABOUT — imagen + texto largo
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($about)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
    <div class="grid lg:grid-cols-[1fr_1.1fr] gap-12 lg:gap-16 items-start">

        {{-- Visual compuesto --}}
        <div class="sr relative max-w-md mx-auto lg:max-w-none">
            <div class="relative aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-lift">
                @if ($about->image)
                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$about->image) }}" alt="{{ $about->title }}" class="absolute inset-0 w-full h-full object-cover">
                @else
                    {{-- Composición orgánica decorativa --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-400 to-brand-600"></div>
                    <div class="absolute -top-10 -right-10 w-72 h-72 rounded-full bg-sun-300/60 blur-2xl"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-coral-300/50 blur-2xl"></div>
                    <p class="absolute inset-0 grid place-items-center text-white font-display font-extrabold text-7xl">C</p>
                @endif
            </div>

            {{-- Stickers flotantes --}}
            <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-lift px-4 py-3 border border-ink-200/70 transform rotate-6 hidden sm:block">
                <p class="font-display font-extrabold text-2xl text-brand-600">12K+</p>
                <p class="text-xs text-ink-500 leading-tight">estudiantes activos</p>
            </div>
            <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lift px-4 py-3 border border-ink-200/70 transform -rotate-6 hidden sm:flex items-center gap-2">
                <p class="text-sun-500 text-lg">★★★★★</p>
                <div class="leading-none">
                    <p class="font-display font-extrabold text-lg text-ink-900">4.9</p>
                    <p class="text-[10px] text-ink-500">satisfacción</p>
                </div>
            </div>
        </div>

        {{-- Texto largo --}}
        <div class="sr s2">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral-100 text-coral-600 text-xs font-semibold uppercase tracking-wider">
                Nuestra historia
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-ink-900 leading-tight mt-5">
                Construimos una academia distinta.
            </h2>
            <div class="prose prose-sm sm:prose-base max-w-none mt-5 text-ink-700">
                {!! Purifier::clean($about->content, 'richtext') !!}
            </div>

            {{-- Lista de valores --}}
            <ul class="mt-7 grid sm:grid-cols-2 gap-3">
                @foreach ([
                    ['fa-people-arrows', 'brand', 'Aprender en comunidad'],
                    ['fa-bullseye',      'coral', 'Práctica antes que teoría'],
                    ['fa-handshake',     'sun',   'Acceso para todos'],
                    ['fa-leaf',          'brand', 'Crecimiento sostenible'],
                ] as [$ic, $c, $txt])
                    <li class="flex items-center gap-3 bg-white border border-ink-200/70 rounded-2xl px-4 py-3 shadow-soft">
                        <span class="grid place-items-center w-9 h-9 rounded-xl
                            @if($c === 'brand') bg-brand-100 text-brand-600
                            @elseif($c === 'coral') bg-coral-100 text-coral-500
                            @else bg-sun-100 text-sun-500 @endif">
                            <i class="fa-solid {{ $ic }}"></i>
                        </span>
                        <span class="text-sm text-ink-700 font-medium">{{ $txt }}</span>
                    </li>
                @endforeach
            </ul>

            @if ($about->button_text)
                <a href="{{ $about->button_url ?: route('courses.index') }}" class="inline-flex items-center gap-2 mt-8 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    {{ $about->button_text }} <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     CONTADORES — full-bleed verde
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($counter)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="sr relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-brand-600 to-brand-800 text-white px-6 sm:px-12 py-12 shadow-lift">
        <div class="blob bg-sun-300/30 w-72 h-72 -top-20 -right-20"></div>
        <div class="blob bg-coral-300/30 w-72 h-72 -bottom-20 -left-20"></div>

        <div class="relative">
            <h2 class="font-display font-extrabold text-3xl text-center max-w-2xl mx-auto">Cifras que nos hacen ilusión</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
                @php
                    $items = [
                        ['title' => $counter->counter_one_title,   'value' => $counter->counter_one_value],
                        ['title' => $counter->counter_two_title,   'value' => $counter->counter_two_value],
                        ['title' => $counter->counter_three_title, 'value' => $counter->counter_three_value],
                        ['title' => $counter->counter_four_title,  'value' => $counter->counter_four_value],
                    ];
                @endphp
                @foreach ($items as $it)
                    @if ($it['value'])
                        <div class="text-center">
                            <p class="font-display font-extrabold text-4xl sm:text-5xl tracking-tight">{{ $it['value'] }}</p>
                            <p class="text-brand-50/85 text-sm mt-2">{{ $it['title'] ?? '' }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     TESTIMONIOS — variados como en home
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($testimonials->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="sr text-center max-w-2xl mx-auto mb-12">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sun-100 text-sun-500 text-xs font-semibold uppercase tracking-wider">
            Voces de la comunidad
        </span>
        <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-ink-900 tracking-tight mt-4">
            Lo que dicen quienes nos eligen
        </h2>
    </div>

    @php $cardBgs = ['bg-white', 'bg-brand-50', 'bg-coral-50']; @endphp
    <div class="grid md:grid-cols-3 gap-5">
        @foreach ($testimonials->take(3) as $i => $t)
            <figure class="card-lift sr s{{ ($i % 3) + 1 }} {{ $cardBgs[$i % 3] }} rounded-3xl border border-ink-200/70 shadow-soft p-6 flex flex-col">
                <p class="text-sun-500 text-lg mb-3">{{ str_repeat('★', max(1, (int) $t->rating)) }}</p>
                <blockquote class="text-ink-700 leading-relaxed flex-1 text-sm">"{{ $t->message }}"</blockquote>
                <figcaption class="flex items-center gap-3 mt-5 pt-5 border-t border-ink-200/70">
                    @if ($t->avatar)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$t->avatar) }}" alt="{{ $t->name }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <span class="grid place-items-center w-10 h-10 rounded-full text-white font-bold bg-gradient-to-br from-brand-500 to-coral-400">
                            {{ strtoupper(substr($t->name, 0, 2)) }}
                        </span>
                    @endif
                    <span class="leading-tight">
                        <span class="block font-semibold text-ink-900 text-sm">{{ $t->name }}</span>
                        <span class="block text-xs text-ink-500">{{ $t->designation }}</span>
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

{{-- CTA final --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 mb-8">
    <div class="sr relative overflow-hidden rounded-[2.5rem] bg-ink-950 text-white px-6 sm:px-12 py-12 shadow-lift text-center">
        <div class="blob bg-brand-600/30 w-72 h-72 -top-20 -right-20"></div>
        <div class="blob bg-coral-500/25 w-72 h-72 -bottom-20 -left-20"></div>
        <div class="relative max-w-2xl mx-auto">
            <h2 class="font-display font-extrabold text-3xl tracking-tight">¿Listo para empezar?</h2>
            <p class="text-white/70 mt-3">Crea tu cuenta gratis y empieza tu primer curso hoy mismo.</p>
            <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-500 text-white hover:bg-brand-400 shadow-soft transition">
                    Crear cuenta gratis <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold bg-white/10 border border-white/15 text-white hover:bg-white/15 transition">
                    Explorar cursos
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
