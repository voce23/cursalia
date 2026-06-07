@extends('layouts.app')

@section('title', 'Catálogo de cursos')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO corto del catálogo
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[24rem] h-[24rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[20rem] h-[20rem] top-10 right-0"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">
        <div class="flex items-center gap-2 text-sm text-ink-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-brand-700">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
            <span class="text-ink-900 font-medium">Cursos</span>
        </div>
        <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight text-ink-900 leading-tight">
            Todos los <span class="text-brand-600">cursos</span>
        </h1>
        <p class="text-ink-500 mt-3 max-w-2xl">
            {{ $courses->total() }} {{ $courses->total() === 1 ? 'curso disponible' : 'cursos disponibles' }} ·
            elige el tuyo y empieza hoy mismo.
        </p>

        {{-- Buscador --}}
        <form action="{{ route('courses.index') }}" method="GET" class="relative max-w-2xl mt-7">
            @foreach (request()->except(['search', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <div class="relative bg-white rounded-full border border-ink-200 shadow-soft flex items-center pl-5 pr-2 py-2 transition focus-within:ring-4 focus-within:ring-brand-100 focus-within:border-brand-400">
                <i class="fa-solid fa-magnifying-glass text-ink-400 mr-3"></i>
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="¿Qué quieres aprender?"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-ink-900 placeholder-ink-400 text-sm sm:text-base py-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold bg-brand-600 text-white hover:bg-brand-700 transition text-sm">
                    Buscar <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     CATÁLOGO con sidebar de filtros
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" x-data="{ filters: false }">

    {{-- Botón filtros móvil --}}
    <div class="lg:hidden flex items-center justify-between mb-5">
        <button @click="filters = !filters" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white border border-ink-200 shadow-soft text-sm font-semibold text-ink-700">
            <i class="fa-solid fa-sliders"></i> Filtros
        </button>
        <form action="{{ route('courses.index') }}" method="GET" class="flex-1 ml-3 min-w-0">
            @foreach (request()->except(['sort', 'page']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-full bg-white border border-ink-200 text-sm text-ink-700 focus:outline-none">
                @php $sort = request('sort'); @endphp
                <option value="" @selected(! $sort)>Más recientes</option>
                <option value="oldest"     @selected($sort === 'oldest')>Más antiguos</option>
                <option value="price_low"  @selected($sort === 'price_low')>Precio: menor primero</option>
                <option value="price_high" @selected($sort === 'price_high')>Precio: mayor primero</option>
                <option value="rating"     @selected($sort === 'rating')>Mejor valorados</option>
            </select>
        </form>
    </div>

    <div class="grid lg:grid-cols-[270px_1fr] gap-8 items-start">

        {{-- ═════════════ SIDEBAR FILTROS ═════════════ --}}
        <aside :class="filters ? 'block' : 'hidden lg:block'" class="lg:sticky lg:top-24">
            <form action="{{ route('courses.index') }}" method="GET" class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5 space-y-6">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                {{-- Encabezado --}}
                <div class="flex items-center justify-between">
                    <h3 class="font-display font-bold text-ink-900 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-brand-600 text-sm"></i> Filtros
                    </h3>
                    @if (collect(request()->only(['category','level','language','price','sort']))->filter()->isNotEmpty())
                        <a href="{{ route('courses.index', request('search') ? ['search' => request('search')] : []) }}"
                           class="text-xs font-semibold text-coral-500 hover:text-coral-600">Limpiar</a>
                    @endif
                </div>

                {{-- Precio --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Precio</p>
                    <div class="space-y-2">
                        @foreach ([['', 'Todos'], ['free', 'Gratis 💚'], ['paid', 'De pago']] as [$val, $lbl])
                            <label class="flex items-center gap-3 cursor-pointer text-sm">
                                <input type="radio" name="price" value="{{ $val }}" @checked(request('price') === $val) onchange="this.form.submit()"
                                    class="w-4 h-4 text-brand-600 border-ink-300 focus:ring-brand-400">
                                <span class="text-ink-700">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Categorías --}}
                @if ($categories->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Categoría</p>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-3 cursor-pointer text-sm">
                                <input type="radio" name="category" value="" @checked(! request('category')) onchange="this.form.submit()"
                                    class="w-4 h-4 text-brand-600 border-ink-300 focus:ring-brand-400">
                                <span class="text-ink-700">Todas</span>
                            </label>
                            @foreach ($categories->take(8) as $cat)
                                <label class="flex items-center gap-3 cursor-pointer text-sm">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" @checked(request('category') === $cat->slug) onchange="this.form.submit()"
                                        class="w-4 h-4 text-brand-600 border-ink-300 focus:ring-brand-400">
                                    <span class="text-ink-700">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Nivel --}}
                @if ($levels->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Nivel</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('courses.index', array_filter([...request()->except(['level','page']), 'level' => null])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ ! request('level') ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-ink-100' }}">Todos</a>
                            @foreach ($levels as $lv)
                                <a href="{{ route('courses.index', array_filter([...request()->except(['level','page']), 'level' => $lv->slug])) }}"
                                   class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ request('level') === $lv->slug ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-ink-100' }}">{{ $lv->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Idioma --}}
                @if ($languages->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-3">Idioma</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('courses.index', array_filter([...request()->except(['language','page']), 'language' => null])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ ! request('language') ? 'bg-coral-400 text-white' : 'bg-cream-2 text-ink-700 hover:bg-ink-100' }}">Todos</a>
                            @foreach ($languages as $lg)
                                <a href="{{ route('courses.index', array_filter([...request()->except(['language','page']), 'language' => $lg->slug])) }}"
                                   class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ request('language') === $lg->slug ? 'bg-coral-400 text-white' : 'bg-cream-2 text-ink-700 hover:bg-ink-100' }}">{{ $lg->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </form>
        </aside>

        {{-- ═════════════ RESULTADOS ═════════════ --}}
        <div>
            {{-- Toolbar desktop --}}
            <div class="hidden lg:flex items-center justify-between mb-5">
                <div class="text-sm text-ink-500">
                    Mostrando <b class="text-ink-900">{{ $courses->firstItem() ?? 0 }}–{{ $courses->lastItem() ?? 0 }}</b>
                    de <b class="text-ink-900">{{ $courses->total() }}</b> cursos
                </div>
                <form action="{{ route('courses.index') }}" method="GET">
                    @foreach (request()->except(['sort', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    @php $sort = request('sort'); @endphp
                    <select name="sort" onchange="this.form.submit()" class="px-4 py-2 rounded-full bg-white border border-ink-200 text-sm text-ink-700 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        <option value="" @selected(! $sort)>Más recientes</option>
                        <option value="oldest"     @selected($sort === 'oldest')>Más antiguos</option>
                        <option value="price_low"  @selected($sort === 'price_low')>Precio: menor</option>
                        <option value="price_high" @selected($sort === 'price_high')>Precio: mayor</option>
                        <option value="rating"     @selected($sort === 'rating')>Mejor valorados</option>
                    </select>
                </form>
            </div>

            {{-- Grid de cursos --}}
            @if ($courses->isNotEmpty())
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach ($courses as $course)
                        <a href="{{ route('courses.show', $course->slug) }}"
                           class="card-lift group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                            {{-- Thumb --}}
                            <div class="relative aspect-video bg-gradient-to-br from-brand-300 to-brand-600">
                                @if ($course->thumbnail)
                                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <span class="absolute inset-0 grid place-items-center text-white font-display font-extrabold text-3xl opacity-90">
                                        {{ strtoupper(substr($course->title, 0, 2)) }}
                                    </span>
                                @endif
                                @if ($course->category)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/95 text-brand-700 text-[10px] font-bold uppercase tracking-wider">{{ $course->category->name }}</span>
                                @endif
                                @if ((float) $course->price === 0.0)
                                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-brand-600 text-white text-[10px] font-bold">Gratis</span>
                                @elseif ($course->discount > 0)
                                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-coral-400 text-white text-[10px] font-bold">Oferta</span>
                                @endif
                            </div>
                            {{-- Contenido --}}
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-display font-bold text-ink-900 leading-snug line-clamp-2 group-hover:text-brand-700 transition">{{ $course->title }}</h3>
                                <p class="text-xs text-ink-500 mt-1.5 flex items-center gap-2">
                                    <span class="grid place-items-center w-5 h-5 rounded-full bg-brand-100 text-brand-600 text-[9px] font-bold">
                                        {{ strtoupper(substr($course->instructor?->name ?? '?', 0, 1)) }}
                                    </span>
                                    {{ $course->instructor?->name ?? 'Instructor' }}
                                </p>
                                <div class="flex items-center gap-3 mt-3 text-xs text-ink-500">
                                    <span><i class="fa-regular fa-circle-play text-brand-500"></i> {{ $course->lessons_count }} lecciones</span>
                                    @if ($course->level)
                                        <span class="w-1 h-1 rounded-full bg-ink-300"></span>
                                        <span>{{ $course->level->name }}</span>
                                    @endif
                                </div>
                                @if ($course->reviews_avg_rating)
                                    <p class="text-xs text-sun-500 mt-2">
                                        {{ str_repeat('★', (int) round($course->reviews_avg_rating)) }}<span class="text-ink-300">{{ str_repeat('★', 5 - (int) round($course->reviews_avg_rating)) }}</span>
                                        <span class="text-ink-500 ml-1">{{ number_format($course->reviews_avg_rating, 1) }}</span>
                                    </p>
                                @endif
                                <div class="mt-auto pt-4 flex items-center justify-between">
                                    <div class="flex items-baseline gap-2">
                                        @if ($course->price > 0)
                                            @if ($course->discount > 0)
                                                <span class="font-display font-extrabold text-lg text-ink-900">${{ number_format($course->discount, 0) }}</span>
                                                <span class="text-xs text-ink-400 line-through">${{ number_format($course->price, 0) }}</span>
                                            @else
                                                <span class="font-display font-extrabold text-lg text-ink-900">${{ number_format($course->price, 0) }}</span>
                                            @endif
                                        @else
                                            <span class="font-display font-extrabold text-lg text-brand-600">Gratis</span>
                                        @endif
                                    </div>
                                    <span class="grid place-items-center w-9 h-9 rounded-full bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                <div class="mt-10">
                    {{ $courses->onEachSide(1)->links() }}
                </div>
            @else
                @php
                    // ¿Hay filtros/búsqueda activos? Distingue "no hay resultados"
                    // de "el catálogo todavía está vacío".
                    $hasFilters = request()->hasany(['search', 'category', 'subcategory', 'level', 'language', 'price', 'sort']);
                @endphp
                @if ($hasFilters)
                    {{-- Búsqueda sin resultados --}}
                    <div class="bg-white rounded-3xl border-2 border-dashed border-ink-200 p-12 text-center">
                        <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
                            <i class="fa-regular fa-face-frown-open text-2xl"></i>
                        </span>
                        <p class="font-display font-bold text-ink-900 mt-5">No encontramos cursos con esos criterios</p>
                        <p class="text-sm text-ink-500 mt-1">Prueba quitando algún filtro o búsqueda.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 mt-6 px-4 py-2.5 rounded-full bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">
                            Ver todos los cursos
                        </a>
                    </div>
                @else
                    {{-- Catálogo todavía vacío: invitar al curso gratis del blog --}}
                    <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-brand-500 to-brand-700 px-6 sm:px-12 py-16 shadow-lift text-center">
                        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
                        <div class="blob bg-coral-300/30 w-72 h-72 -bottom-20 -left-20"></div>
                        <div class="relative max-w-2xl mx-auto">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-semibold">
                                <i class="fa-solid fa-seedling"></i> Estamos empezando
                            </span>
                            <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight leading-tight mt-5">
                                El catálogo de cursos llega pronto
                            </h2>
                            <p class="text-brand-50 text-lg mt-4 leading-relaxed">
                                Mientras preparamos los primeros cursos, te invitamos a nuestro curso gratuito del blog: aprende a montar tu propia academia online paso a paso, sin programar.
                            </p>
                            <a href="{{ route('blog.index', ['category' => \App\Models\Blog::COURSE_CATEGORY_SLUG]) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-brand-700 font-bold mt-7 hover:bg-brand-50 transition">
                                <i class="fa-solid fa-graduation-cap"></i> Ver el curso gratis
                            </a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</section>
@endsection
