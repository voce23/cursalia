@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════
     HERO Cursalia — centrado, con buscador, aligerado (2 stickers máx)
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    {{-- Blobs orgánicos de fondo --}}
    <div class="blob bg-brand-200 w-[30rem] h-[30rem] -top-32 -left-20"></div>
    <div class="blob bg-coral-200 w-[26rem] h-[26rem] top-16 -right-16"></div>
    <div class="blob bg-sun-200 w-[22rem] h-[22rem] top-60 left-1/3"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 sm:pt-20 sm:pb-28 text-center">

        {{-- Badge --}}
        <div class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 font-bold text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Nuevo
            </span>
            <span class="text-ink-700">{{ $hero?->badge_text ?: 'Aprende a tu ritmo' }}</span>
        </div>

        {{-- Titular --}}
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-5xl sm:text-6xl lg:text-7xl leading-[1.05] mt-7 text-ink-900">
            {{ $hero?->title ?: '¿Qué quieres' }}<br>
            <span class="text-brand-600">{{ $hero?->highlight_text ?: 'aprender hoy?' }}</span>
        </h1>

        {{-- Subtítulo --}}
        <p class="sr s2 text-base sm:text-lg text-ink-500 leading-relaxed mt-6 max-w-xl mx-auto">
            {{ $hero?->description ?: 'Cursos prácticos creados por mentores reales. Estudia a tu ritmo, construye proyectos de verdad y obtén un certificado al terminar.' }}
        </p>

        {{-- Buscador protagonista --}}
        <form action="{{ route('courses.index') }}" method="GET" class="sr s3 relative max-w-2xl mx-auto mt-10">
            <div class="relative bg-white rounded-full border border-ink-200 shadow-lift flex items-center pl-5 pr-2 py-2 transition focus-within:ring-4 focus-within:ring-brand-100 focus-within:border-brand-400">
                <i class="fa-solid fa-magnifying-glass text-ink-400 mr-3"></i>
                <input type="search" name="search" placeholder="Prueba con: diseño, JavaScript, marketing…"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-ink-900 placeholder-ink-400 text-sm sm:text-base py-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition text-sm">
                    <span class="hidden sm:inline">Explorar</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

        {{-- Píldoras de categorías populares --}}
        @if ($categories->isNotEmpty())
            <div class="sr s4 flex flex-wrap items-center justify-center gap-2 mt-5">
                <span class="text-xs text-ink-400 font-medium mr-1">Populares:</span>
                @foreach ($categories->take(5) as $cat)
                    <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/80 border border-ink-200/70 text-xs font-medium text-ink-700 hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200 transition">
                        <i class="fa-solid fa-tag text-[10px] text-brand-500"></i>
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Stickers flotantes — solo en pantallas MUY anchas (xl+) y bien lejos del título --}}
        <div class="hidden xl:block">
            {{-- Sticker sin código (abajo-izquierda, lejos del titular) --}}
            <div class="sr s3 absolute bottom-10 left-0 transform -rotate-[6deg] bg-white border border-ink-200/70 rounded-2xl shadow-lift px-4 py-3 flex items-center gap-3 max-w-[200px] pointer-events-none">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-brand-100 text-brand-600 shrink-0">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </span>
                <div class="text-left">
                    <p class="text-xs font-bold text-ink-900 leading-tight">A tu ritmo<br>cuando quieras</p>
                </div>
            </div>
            {{-- Sticker certificado (abajo-derecha, lejos del titular) --}}
            <div class="sr s4 absolute bottom-10 right-0 transform rotate-[5deg] bg-white border border-ink-200/70 rounded-2xl shadow-lift px-4 py-3 flex items-center gap-3 max-w-[210px] pointer-events-none">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-coral-100 text-coral-500 shrink-0">
                    <i class="fa-solid fa-award"></i>
                </span>
                <div class="text-left">
                    <p class="text-[11px] font-bold text-ink-900 leading-tight">Certificado</p>
                    <p class="text-[10px] text-ink-500">al completar el curso</p>
                </div>
            </div>
        </div>

        {{-- 3 propuestas de valor reales (sin métricas inventadas) --}}
        <div class="sr s4 grid grid-cols-3 gap-4 sm:gap-8 mt-14 max-w-md mx-auto">
            @php
                $stats = [
                    ['A tu ritmo',  'Sin horarios'],
                    ['Mentores',    'Con experiencia'],
                    ['Certificado', 'Al terminar'],
                ];
            @endphp
            @foreach ($stats as [$n, $lbl])
                <div>
                    <p class="font-display font-extrabold text-2xl sm:text-3xl text-ink-900">{{ $n }}</p>
                    <p class="text-xs sm:text-sm text-ink-500 mt-1">{{ $lbl }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     CUATRO RAZONES — sección oscura full-bleed con pasos numerados 01–04
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-ink-950 text-white py-20 sm:py-24 overflow-hidden">
    <div class="blob bg-brand-600/30 w-[28rem] h-[28rem] top-0 left-1/4"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="sr max-w-2xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold uppercase tracking-wider text-brand-300">
                Por qué Cursalia
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight mt-5">
                Cuatro razones para<br>empezar hoy.
            </h2>
        </div>

        @php
            $featureItems = ($features ?? collect())->isNotEmpty() ? $features : collect([
                (object) ['title' => 'Aprende a tu ritmo', 'description' => 'Acceso de por vida, disponible 24/7 desde cualquier dispositivo. Pausa y retoma cuando quieras.'],
                (object) ['title' => 'Proyectos reales', 'description' => 'Construye un portafolio que impresiona. Cada curso termina con un proyecto que puedes mostrar.'],
                (object) ['title' => 'Mentores expertos', 'description' => 'Instructores que trabajan en la industria y resuelven tus dudas en la comunidad.'],
                (object) ['title' => 'Certificado verificable', 'description' => 'Al completar recibes un certificado que puedes compartir en LinkedIn y tu CV.'],
            ]);
            $colors = ['brand', 'coral', 'sun', 'brand'];
        @endphp

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-14">
            @foreach ($featureItems as $i => $f)
                @php $c = $colors[$i % 4]; @endphp
                <div class="sr s{{ ($i % 4) + 1 }} bg-white/[0.04] border border-white/10 rounded-3xl p-6 hover:bg-white/[0.06] transition">
                    <p class="font-display font-extrabold text-3xl
                        @if($c === 'brand') text-brand-400
                        @elseif($c === 'coral') text-coral-400
                        @else text-sun-400 @endif">{{ sprintf('%02d', $i + 1) }}</p>
                    <h3 class="font-display font-bold text-lg mt-5">{{ $f->title }}</h3>
                    <p class="text-white/65 text-sm leading-relaxed mt-2">{{ $f->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     CATEGORÍAS — Bento mosaico de celdas de distintos tamaños y colores
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
    <div class="sr flex flex-wrap items-end justify-between gap-4 mb-10">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral-100 text-coral-600 text-xs font-semibold uppercase tracking-wider">
                Explora
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-4 text-ink-900">
                {{ $featuredCategorySection?->title ?: 'Elige por dónde empezar' }}
            </h2>
            <p class="text-ink-500 mt-3 max-w-xl">
                {{ $featuredCategorySection?->subtitle ?: 'Cada mundo tiene su puerta. Toca la que te llame.' }}
            </p>
        </div>
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-ink-200 hover:bg-cream-2 text-ink-700 text-sm font-semibold transition">
            Ver todas <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

    {{-- Bento: 6 categorías con tamaños distintos --}}
    @php
        $cats = $categories->take(6);
        $bento = [
            // [span_class, bg_class, text_color, icon]
            ['sm:col-span-2 lg:row-span-2',                  'bg-brand-50 hover:bg-brand-100',  'text-brand-700',  'fa-pen-ruler'],
            ['',                                              'bg-coral-50 hover:bg-coral-100',  'text-coral-600',  'fa-code'],
            ['',                                              'bg-sun-100 hover:bg-sun-200',     'text-sun-500',    'fa-bullhorn'],
            ['sm:col-span-2',                                 'bg-white hover:bg-cream-2',       'text-ink-900',    'fa-briefcase'],
            ['',                                              'bg-coral-50 hover:bg-coral-100',  'text-coral-600',  'fa-camera'],
            ['',                                              'bg-brand-50 hover:bg-brand-100',  'text-brand-700',  'fa-music'],
        ];
    @endphp

    @if ($cats->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-4 auto-rows-[160px] sm:auto-rows-[180px]">
            @foreach ($cats as $i => $cat)
                @php [$span, $bg, $txt, $ic] = $bento[$i % count($bento)]; @endphp
                <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                   class="card-lift sr s{{ ($i % 4) + 1 }} {{ $span }} {{ $bg }} rounded-3xl border border-ink-200/70 p-5 flex flex-col justify-between transition group">
                    <span class="grid place-items-center w-11 h-11 rounded-2xl bg-white {{ $txt }} shadow-soft">
                        <i class="fa-solid {{ $ic }}"></i>
                    </span>
                    <div>
                        <h3 class="font-display font-bold text-ink-900 leading-snug group-hover:translate-x-1 transition">{{ $cat->name }}</h3>
                        <p class="text-xs text-ink-500 mt-1">{{ $cat->all_courses_count }} cursos</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     APRENDIZAJE PARA TODOS — composición asimétrica con blobs
     ═══════════════════════════════════════════════════════════════════════ --}}
@if ($aboutSection)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div class="sr order-2 lg:order-1">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider">
                Sobre nosotros
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-tight mt-5 text-ink-900">
                {{ $aboutSection->title }}
            </h2>
            @if ($aboutSection->subtitle)
                <p class="text-ink-500 text-lg mt-5">{{ $aboutSection->subtitle }}</p>
            @endif
            <div class="prose prose-sm mt-5 text-ink-700">
                {!! Purifier::clean($aboutSection->content, 'richtext') !!}
            </div>
            <ul class="mt-7 space-y-3">
                @foreach (['Contenido siempre actualizado','Soporte humano y cercano','Comunidad multidisciplinaria','Acceso multidispositivo'] as $b)
                    <li class="flex items-center gap-3 text-ink-700">
                        <span class="grid place-items-center w-6 h-6 rounded-full bg-brand-100 text-brand-600 text-xs">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        {{ $b }}
                    </li>
                @endforeach
            </ul>
            @if ($aboutSection->button_text)
                <a href="{{ $aboutSection->button_url ?: '#' }}" class="inline-flex items-center gap-2 mt-8 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    {{ $aboutSection->button_text }} <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>

        {{-- Composición visual limpia: imagen central + 4 stickers --}}
        <div class="sr s2 order-1 lg:order-2 relative">
            <div class="relative aspect-square max-w-md mx-auto">

                {{-- Imagen principal (o composición decorativa) --}}
                <div class="absolute inset-4 sm:inset-8 rounded-[2.5rem] overflow-hidden shadow-lift">
                    @if ($aboutSection->image)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$aboutSection->image) }}" alt="{{ $aboutSection->title }}" class="absolute inset-0 w-full h-full object-cover">
                    @else
                        {{-- Composición sin imagen: gradient + iconos decorativos --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700"></div>
                        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-sun-300/60 blur-2xl"></div>
                        <div class="absolute -bottom-20 -left-20 w-56 h-56 rounded-full bg-coral-300/50 blur-2xl"></div>
                        {{-- Iconos sutiles --}}
                        <div class="absolute inset-0 grid place-items-center">
                            <div class="grid grid-cols-2 gap-6 opacity-90">
                                <span class="grid place-items-center w-16 h-16 rounded-3xl bg-white/95 text-brand-600 shadow-soft text-2xl"><i class="fa-solid fa-book-open"></i></span>
                                <span class="grid place-items-center w-16 h-16 rounded-3xl bg-white/95 text-coral-500 shadow-soft text-2xl mt-6"><i class="fa-solid fa-graduation-cap"></i></span>
                                <span class="grid place-items-center w-16 h-16 rounded-3xl bg-white/95 text-sun-500 shadow-soft text-2xl mt-6"><i class="fa-solid fa-lightbulb"></i></span>
                                <span class="grid place-items-center w-16 h-16 rounded-3xl bg-white/95 text-brand-600 shadow-soft text-2xl"><i class="fa-solid fa-rocket"></i></span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sticker proyectos (esquina inferior izquierda) --}}
                <div class="absolute -left-1 sm:-left-3 bottom-2 sm:-bottom-3 bg-white rounded-2xl shadow-lift px-4 py-3 border border-ink-200/70 transform -rotate-3">
                    <p class="font-display font-extrabold text-2xl sm:text-3xl text-brand-600 leading-none"><i class="fa-solid fa-laptop-code"></i></p>
                    <p class="text-xs text-ink-500 leading-tight mt-1">Proyectos reales</p>
                </div>

                {{-- Sticker mentores (esquina superior derecha) --}}
                <div class="absolute -right-1 sm:-right-3 top-2 sm:-top-3 bg-white rounded-2xl shadow-lift px-4 py-3 border border-ink-200/70 transform rotate-6">
                    <span class="text-brand-500 text-sm leading-none"><i class="fa-solid fa-chalkboard-user"></i></span>
                    <p class="font-display font-extrabold text-base sm:text-lg text-ink-900 leading-none mt-1">Mentores<span class="text-sm text-ink-400"> reales</span></p>
                </div>

                {{-- Sticker a tu ritmo (esquina superior izquierda) --}}
                <div class="hidden sm:flex absolute -left-3 top-6 bg-white rounded-2xl shadow-lift px-3 py-2.5 border border-ink-200/70 items-center gap-2 transform rotate-3">
                    <span class="grid place-items-center w-8 h-8 rounded-xl bg-brand-100 text-brand-600">
                        <i class="fa-solid fa-clock text-sm"></i>
                    </span>
                    <p class="text-xs font-bold text-ink-900 leading-tight">A tu ritmo<br><span class="text-[10px] text-ink-500 font-normal">sin horarios</span></p>
                </div>

                {{-- Sticker comunidad (esquina inferior derecha) --}}
                <div class="hidden sm:flex absolute -right-3 bottom-6 bg-white rounded-2xl shadow-lift px-3 py-2.5 border border-ink-200/70 items-center gap-2 transform -rotate-3">
                    <span class="grid place-items-center w-8 h-8 rounded-xl bg-coral-100 text-coral-500">
                        <i class="fa-solid fa-users text-sm"></i>
                    </span>
                    <p class="text-xs font-bold text-ink-900 leading-tight">Comunidad<br><span class="text-[10px] text-ink-500 font-normal">de estudiantes</span></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════
     CURSOS DESTACADOS — layout asimétrico (1 grande + lista)
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
    @if ($featuredCourses->isNotEmpty())
    <div class="sr flex flex-wrap items-end justify-between gap-4 mb-10">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sun-100 text-sun-500 text-xs font-semibold uppercase tracking-wider">
                Lo más elegido
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-4 text-ink-900">
                {{ $latestCourseSection?->title ?: 'Cursos destacados' }}
            </h2>
            <p class="text-ink-500 mt-3 max-w-xl">
                {{ $latestCourseSection?->subtitle ?: 'Una selección de favoritos para empezar hoy mismo.' }}
            </p>
        </div>
        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-ink-200 hover:bg-cream-2 text-ink-700 text-sm font-semibold transition">
            Ver todos <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </div>

        @php $main = $featuredCourses->first(); $rest = $featuredCourses->skip(1)->take(3); @endphp
        <div class="grid lg:grid-cols-2 gap-5">

            {{-- Tarjeta GRANDE --}}
            <a href="{{ route('courses.show', $main->slug) }}" class="card-lift sr group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                <div class="relative aspect-video bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700 overflow-hidden">
                    @if ($main->thumbnail)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$main->thumbnail) }}" alt="{{ $main->title }}" class="absolute inset-0 w-full h-full object-cover">
                    @else
                        {{-- Placeholder cuando no hay imagen: gradient + título superpuesto --}}
                        <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-sun-300/50 blur-3xl"></div>
                        <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-coral-300/40 blur-3xl"></div>
                        <div class="absolute inset-0 grid place-items-center p-6">
                            <div class="text-center">
                                <i class="fa-solid fa-graduation-cap text-white/80 text-3xl mb-3"></i>
                                <p class="font-display font-extrabold text-2xl sm:text-3xl text-white leading-tight line-clamp-2 px-4">{{ $main->title }}</p>
                            </div>
                        </div>
                    @endif
                    {{-- Badges (siempre visibles encima) --}}
                    @if ($main->category)
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-white/95 text-brand-700 text-xs font-bold shadow-soft z-10">{{ $main->category->name }}</span>
                    @endif
                    @if ((float) $main->price === 0.0)
                        <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold shadow-soft z-10">Gratis</span>
                    @elseif ($main->discount > 0)
                        <span class="absolute top-4 right-4 px-3 py-1 rounded-full bg-coral-400 text-white text-xs font-bold shadow-soft z-10">Oferta</span>
                    @endif
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col">
                    <h3 class="font-display font-extrabold text-xl sm:text-2xl text-ink-900 leading-tight group-hover:text-brand-700 transition">{{ $main->title }}</h3>
                    <p class="text-sm text-ink-500 mt-2 line-clamp-2">{{ $main->seo_description ?: 'Aprende paso a paso con un instructor profesional.' }}</p>
                    <div class="flex items-center gap-3 mt-4 text-xs text-ink-500">
                        <span><i class="fa-regular fa-circle-play text-brand-500"></i> {{ $main->lessons_count }} lecciones</span>
                        @if ($main->duration)
                            <span class="w-1 h-1 rounded-full bg-ink-300"></span>
                            <span>{{ $main->duration }}</span>
                        @endif
                    </div>
                    <div class="mt-auto pt-6 flex items-end justify-between">
                        <div class="flex items-baseline gap-2">
                            @if ($main->price > 0)
                                @if ($main->discount > 0)
                                    <span class="font-display font-extrabold text-2xl text-ink-900">${{ number_format($main->discount, 0) }}</span>
                                    <span class="text-sm text-ink-400 line-through">${{ number_format($main->price, 0) }}</span>
                                @else
                                    <span class="font-display font-extrabold text-2xl text-ink-900">${{ number_format($main->price, 0) }}</span>
                                @endif
                            @else
                                <span class="font-display font-extrabold text-2xl text-brand-600">Gratis</span>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-brand-600 text-white text-sm font-bold group-hover:bg-brand-700 transition">
                            Ver curso <i class="fa-solid fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            {{-- Lista de 3 cursos al lado --}}
            <div class="grid gap-4 content-start">
                @foreach ($rest as $c)
                    <a href="{{ route('courses.show', $c->slug) }}" class="card-lift sr s{{ $loop->iteration }} group bg-white rounded-3xl border border-ink-200/70 shadow-soft p-4 flex gap-4 items-center">
                        @php
                            $miniGrads = [
                                'bg-gradient-to-br from-brand-400 to-brand-600',
                                'bg-gradient-to-br from-coral-400 to-coral-600',
                                'bg-gradient-to-br from-sun-400 to-coral-400',
                            ];
                            $g = $miniGrads[$loop->index % 3];
                        @endphp
                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden {{ $g }} shrink-0">
                            @if ($c->thumbnail)
                                <img loading="lazy" decoding="async" src="{{ asset('storage/'.$c->thumbnail) }}" alt="{{ $c->title }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <span class="absolute inset-0 grid place-items-center text-white font-display font-extrabold text-2xl drop-shadow">
                                    {{ strtoupper(substr($c->title, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            @if ($c->category)
                                <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-brand-600">{{ $c->category->name }}</span>
                            @endif
                            <h4 class="font-display font-bold text-ink-900 leading-snug line-clamp-2 mt-1 group-hover:text-brand-700 transition">{{ $c->title }}</h4>
                            <p class="text-xs text-ink-500 mt-1">
                                <i class="fa-regular fa-circle-play"></i> {{ $c->lessons_count }} lecciones
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                @if ($c->price > 0)
                                    <span class="font-display font-extrabold text-ink-900">${{ number_format($c->discount ?: $c->price, 0) }}</span>
                                @else
                                    <span class="font-display font-extrabold text-brand-600 text-sm">Gratis</span>
                                @endif
                                <i class="fa-solid fa-arrow-right text-ink-300 group-hover:text-brand-600 group-hover:translate-x-1 transition"></i>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- Sin cursos en catálogo: en vez de un hueco triste, invitamos al
             curso GRATIS del blog (que es contenido real y nuestro foco actual). --}}
        <div class="sr relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-brand-500 to-brand-700 px-6 sm:px-12 py-14 sm:py-16 shadow-lift text-center">
            <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
            <div class="blob bg-coral-300/30 w-72 h-72 -bottom-20 -left-20"></div>
            <div class="relative max-w-2xl mx-auto">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-semibold">
                    <i class="fa-solid fa-graduation-cap"></i> Curso gratis · 14 lecciones
                </span>
                <h3 class="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight leading-tight mt-5">
                    Aprende a crear tu academia online, paso a paso
                </h3>
                <p class="text-brand-50 text-lg mt-4 leading-relaxed">
                    Estamos preparando el catálogo de cursos. Mientras tanto, empieza por nuestro curso gratuito: monta tu propia plataforma desde cero, sin programar.
                </p>
                <a href="{{ route('blog.index', ['category' => \App\Models\Blog::COURSE_CATEGORY_SLUG]) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-brand-700 font-bold mt-7 hover:bg-brand-50 transition">
                    <i class="fa-solid fa-arrow-right"></i> Empezar el curso gratis
                </a>
            </div>
        </div>
    @endif
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     NEWSLETTER — banner verde full-bleed
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="sr relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-brand-500 to-brand-700 px-6 sm:px-12 py-12 sm:py-14 shadow-lift">
        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
        <div class="blob bg-coral-300/30 w-72 h-72 -bottom-20 -left-20"></div>

        <div class="relative max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-semibold">
                <i class="fa-solid fa-gift"></i> Gratis cada semana
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight leading-tight mt-5">
                {{ $homeMiscSection?->newsletter_title ?: 'Aprende algo nuevo cada semana' }}
            </h2>
            <p class="text-white/85 mt-3 max-w-xl">
                {{ $homeMiscSection?->newsletter_subtitle ?: 'Recibe cursos nuevos, recursos gratis y descuentos exclusivos directo en tu correo.' }}
            </p>

            @if (session('status'))
                <p class="mt-4 inline-flex items-center gap-2 bg-white/15 backdrop-blur text-white text-sm px-4 py-2 rounded-full">
                    <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                </p>
            @endif

            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-2 mt-6 max-w-xl">
                @csrf
                <input type="email" name="email" required placeholder="tucorreo@ejemplo.com"
                    class="flex-1 px-5 py-3.5 rounded-full bg-white/95 text-ink-900 placeholder-ink-400 border-0 focus:outline-none focus:ring-4 focus:ring-white/30">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full font-bold bg-ink-900 text-white hover:bg-ink-700 transition whitespace-nowrap">
                    Suscribirme <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     CÓMO SE APRENDE — bloque editorial con video placeholder
     ═══════════════════════════════════════════════════════════════════════ --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 text-center">
    <span class="sr inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral-100 text-coral-600 text-xs font-semibold uppercase tracking-wider">
        Conocenos en 2 minutos
    </span>
    <h2 class="sr s1 font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-5 text-ink-900">
        Así se aprende con nosotros
    </h2>
    <p class="sr s2 text-ink-500 mt-4 max-w-xl mx-auto">
        Una mirada rápida a cómo nuestros estudiantes avanzan, desde la primera lección hasta el proyecto final.
    </p>
    <div class="sr s3 relative aspect-video rounded-[2rem] bg-ink-950 overflow-hidden mt-10 shadow-lift grid place-items-center group cursor-pointer">
        <div class="blob bg-brand-600/30 w-72 h-72 top-0 left-0"></div>
        <div class="absolute font-display font-extrabold text-3xl sm:text-5xl text-white/90 select-none">
            Video de <span class="inline-flex items-center gap-3">
                <span class="grid place-items-center w-14 h-14 sm:w-20 sm:h-20 rounded-full bg-brand-500 group-hover:bg-brand-400 transition shadow-lift">
                    <i class="fa-solid fa-play text-white text-lg sm:text-2xl ml-1"></i>
                </span>
            </span> sentación
        </div>
        <span class="absolute bottom-4 left-4 text-xs text-white/50 bg-white/10 backdrop-blur px-3 py-1 rounded-full">2:14 · Tour de la academia</span>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════════
     MARCAS — fila de logos
     ═══════════════════════════════════════════════════════════════════════ --}}
@if ($brands->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-ink-200/60">
    <p class="sr text-center text-xs uppercase tracking-[0.2em] text-ink-400 mb-8">Empresas que contratan a nuestros egresados</p>
    <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-6 opacity-70">
        @foreach ($brands as $brand)
            <span class="sr s{{ ($loop->index % 3) + 1 }} font-display font-bold text-xl text-ink-400 hover:text-brand-600 transition">{{ $brand->name }}</span>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════
     INSTRUCTORES DESTACADOS
     ═══════════════════════════════════════════════════════════════════════ --}}
@if ($featuredInstructors->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
    <div class="sr flex flex-wrap items-end justify-between gap-4 mb-10">
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider">
                Quién enseña
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-4 text-ink-900">
                Instructores destacados
            </h2>
            <p class="text-ink-500 mt-3 max-w-xl">
                Profesionales en activo que comparten lo que saben.
            </p>
        </div>
    </div>

    @php $avatarColors = ['brand', 'coral', 'sun', 'brand']; @endphp
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ($featuredInstructors->take(4) as $i => $item)
            @php $c = $avatarColors[$i % 4]; $u = $item->user; @endphp
            <div class="card-lift sr s{{ ($i % 4) + 1 }} bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 text-center">
                @if ($u?->image)
                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$u->image) }}" alt="{{ $u->name }}" class="w-20 h-20 rounded-full object-cover mx-auto">
                @else
                    <span class="grid place-items-center mx-auto w-20 h-20 rounded-full font-display font-extrabold text-2xl text-white shadow-soft
                        @if($c === 'brand') bg-gradient-to-br from-brand-400 to-brand-600
                        @elseif($c === 'coral') bg-gradient-to-br from-coral-300 to-coral-500
                        @else bg-gradient-to-br from-sun-300 to-sun-500 @endif">
                        {{ strtoupper(substr($u?->name ?? 'I', 0, 2)) }}
                    </span>
                @endif
                <h3 class="font-display font-bold text-ink-900 mt-4">{{ $u?->name }}</h3>
                <p class="text-sm text-ink-500 mt-1">{{ $u?->headline ?? 'Instructor' }}</p>
                <div class="flex items-center justify-center gap-3 mt-4 text-ink-300">
                    <i class="fa-brands fa-linkedin-in hover:text-brand-600 cursor-pointer"></i>
                    <i class="fa-brands fa-x-twitter hover:text-ink-900 cursor-pointer"></i>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════
     TESTIMONIOS — 3 cards con tamaños/colores variados
     ═══════════════════════════════════════════════════════════════════════ --}}
@if ($testimonials->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">
    <div class="sr text-center max-w-2xl mx-auto mb-12">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sun-100 text-sun-500 text-xs font-semibold uppercase tracking-wider">
            Testimonios
        </span>
        <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-4 text-ink-900">
            Lo que dicen nuestros estudiantes
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

{{-- (Sección "¿Quieres ser instructor?" retirada en FREE: el multi-instructor es parte PRO.) --}}

@endsection
