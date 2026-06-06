@extends('layouts.app')

@php
    $isCourseHub = request('category') === \App\Models\Blog::COURSE_CATEGORY_SLUG;
    $courseTitle = 'Curso Cursalia: construye tu propia academia online (gratis)';
    $courseDesc  = 'Curso gratis paso a paso en español para montar tu propia plataforma de cursos online sin pagar Hotmart, Thinkific ni Kajabi. 14 lecciones, sin saber programar.';
@endphp

@section('title', $isCourseHub ? $courseTitle : 'Blog')
@if ($isCourseHub)
    @section('description', $courseDesc)
@endif

{{-- ════════════════════ Schema.org Course (solo en hub del curso) ════════════════════ --}}
@if ($isCourseHub)
@push('head')
@php
    $lessons = \App\Models\Blog::query()
        ->where('status', 'published')
        ->whereNotNull('published_at')
        ->whereHas('category', fn ($q) => $q->where('slug', \App\Models\Blog::COURSE_CATEGORY_SLUG))
        ->orderBy('slug')
        ->get(['id', 'title', 'slug', 'summary', 'published_at', 'admin_id']);

    $author = $lessons->first()?->author;
    $publisher = $generalSetting->site_name ?? 'Cursalia';

    $courseLd = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Course',
        '@id'         => url('/blog?category='.\App\Models\Blog::COURSE_CATEGORY_SLUG).'#course',
        'name'        => $courseTitle,
        'description' => $courseDesc,
        'url'         => url('/blog?category='.\App\Models\Blog::COURSE_CATEGORY_SLUG),
        'inLanguage'  => 'es',
        'isAccessibleForFree' => true,
        'educationalLevel'    => 'Beginner',
        'learningResourceType' => 'Course',
        'provider' => [
            '@type' => 'Organization',
            'name'  => $publisher,
            'url'   => url('/'),
            'sameAs' => collect($socialLinks ?? [])->pluck('url')->filter()->values()->all(),
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => '0',
            'priceCurrency' => 'EUR',
            'category' => 'Free',
        ],
        'hasCourseInstance' => [
            '@type'           => 'CourseInstance',
            'courseMode'      => 'online',
            'courseWorkload'  => 'PT3H', // 3 horas por semana orientativo
        ],
    ];

    if ($author) {
        $courseLd['author'] = array_filter([
            '@type' => 'Person',
            'name'  => $author->name,
            'url'   => url('/sobre-el-autor'),
            'image' => $author->avatar_url,
            'jobTitle' => $author->headline,
        ]);
    }

    if ($lessons->isNotEmpty()) {
        $courseLd['hasPart'] = $lessons->map(fn ($l) => [
            '@type'         => 'LearningResource',
            'name'          => $l->title,
            'url'           => url('/blog/'.$l->slug),
            'description'   => \Illuminate\Support\Str::limit(strip_tags($l->summary ?: ''), 200),
            'datePublished' => optional($l->published_at)->toAtomString(),
            'learningResourceType' => 'Lesson',
        ])->values()->all();
        $courseLd['numberOfCredits'] = $lessons->count();
    }
@endphp
<script type="application/ld+json">
{!! json_encode($courseLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
@endif

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO BLOG
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[26rem] h-[26rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[22rem] h-[22rem] top-32 right-0"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10 text-center">
        <span class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <i class="fa-solid fa-pen-nib"></i> Blog Cursalia
        </span>
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-6 text-ink-900">
            Aprende algo nuevo, <span class="text-brand-600">cada semana.</span>
        </h1>
        <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-5 max-w-2xl mx-auto">
            Tutoriales, novedades y consejos prácticos de nuestro equipo y de los mentores.
        </p>

        {{-- Buscador --}}
        <form action="{{ route('blog.index') }}" method="GET" class="sr s3 relative max-w-2xl mx-auto mt-8">
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative bg-white rounded-full border border-ink-200 shadow-soft flex items-center pl-5 pr-2 py-2 transition focus-within:ring-4 focus-within:ring-brand-100 focus-within:border-brand-400">
                <i class="fa-solid fa-magnifying-glass text-ink-400 mr-3"></i>
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="Busca un artículo, un tema…"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-ink-900 placeholder-ink-400 text-sm sm:text-base py-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold bg-brand-600 text-white hover:bg-brand-700 transition text-sm">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     LAYOUT CON SIDEBAR
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="grid lg:grid-cols-[1fr_280px] gap-10 items-start">

        {{-- ═══════════════ LISTADO ═══════════════ --}}
        <div>
            {{-- Filtros activos --}}
            @if (request('search') || request('category'))
                <div class="flex items-center flex-wrap gap-2 mb-6">
                    <span class="text-xs text-ink-500 font-medium">Filtros:</span>
                    @if (request('search'))
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold">
                            <i class="fa-solid fa-magnifying-glass text-[10px]"></i> "{{ request('search') }}"
                        </span>
                    @endif
                    @if (request('category'))
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral-50 text-coral-600 text-xs font-semibold">
                            <i class="fa-solid fa-tag text-[10px]"></i> {{ request('category') }}
                        </span>
                    @endif
                    <a href="{{ route('blog.index') }}" class="text-xs font-semibold text-coral-500 hover:text-coral-600">Limpiar</a>
                </div>
            @endif

            @if ($blogs->isNotEmpty())
                @php $featured = $blogs->first(); $rest = $blogs->skip(1); @endphp

                {{-- Post DESTACADO grande (solo en página 1) --}}
                @if ($blogs->currentPage() === 1)
                    <a href="{{ route('blog.show', $featured->slug) }}" class="card-lift group block bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden mb-8">
                        <div class="grid md:grid-cols-2">
                            <div class="relative aspect-video md:aspect-auto {{ $featured->thumbnail_is_svg ? 'bg-white' : 'bg-gradient-to-br from-brand-300 to-brand-600' }}">
                                @if ($featured->thumbnail)
                                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$featured->thumbnail) }}" alt="{{ $featured->title }}" class="absolute inset-0 w-full h-full {{ $featured->thumbnail_fit_class }}">
                                @endif
                                <span class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/95 text-brand-700 text-[11px] font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-star text-sun-500 text-[10px]"></i> Destacado
                                </span>
                            </div>
                            <div class="p-6 sm:p-8 flex flex-col">
                                @if ($featured->category)
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider self-start"
                                          style="background:{{ $featured->category->color }}22; color:{{ $featured->category->color }}">
                                        {{ $featured->category->name }}
                                    </span>
                                @endif
                                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-ink-900 leading-tight mt-4 group-hover:text-brand-700 transition">{{ $featured->title }}</h2>
                                @if ($featured->summary)
                                    <p class="text-ink-500 mt-3 leading-relaxed line-clamp-3">{{ $featured->summary }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-auto pt-6 text-xs text-ink-500">
                                    <span class="grid place-items-center w-7 h-7 rounded-full bg-brand-100 text-brand-600 font-bold">
                                        {{ strtoupper(substr($featured->author?->name ?? '?', 0, 1)) }}
                                    </span>
                                    <span>{{ $featured->author?->name ?? 'Cursalia' }}</span>
                                    <span class="w-1 h-1 rounded-full bg-ink-300"></span>
                                    <span>{{ $featured->published_at?->translatedFormat('d \d\e F') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endif

                {{-- Grid 2 columnas con el resto --}}
                @php $list = $blogs->currentPage() === 1 ? $rest : $blogs; @endphp
                @if ($list->isNotEmpty())
                    <div class="grid sm:grid-cols-2 gap-5">
                        @foreach ($list as $b)
                            <a href="{{ route('blog.show', $b->slug) }}" class="card-lift group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                                <div class="relative aspect-video {{ $b->thumbnail_is_svg ? 'bg-white' : 'bg-gradient-to-br from-coral-300 to-sun-400' }}">
                                    @if ($b->thumbnail)
                                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$b->thumbnail) }}" alt="{{ $b->title }}" class="absolute inset-0 w-full h-full {{ $b->thumbnail_fit_class }}">
                                    @else
                                        <span class="absolute inset-0 grid place-items-center text-white font-display font-extrabold text-3xl opacity-90">
                                            {{ strtoupper(substr($b->title, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-5 flex-1 flex flex-col">
                                    @if ($b->category)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider self-start"
                                              style="background:{{ $b->category->color }}22; color:{{ $b->category->color }}">
                                            {{ $b->category->name }}
                                        </span>
                                    @endif
                                    <h3 class="font-display font-bold text-ink-900 leading-snug line-clamp-2 mt-3 group-hover:text-brand-700 transition">{{ $b->title }}</h3>
                                    @if ($b->summary)
                                        <p class="text-sm text-ink-500 mt-2 line-clamp-2">{{ $b->summary }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-auto pt-4 text-xs text-ink-500">
                                        <span>{{ $b->author?->name ?? 'Cursalia' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-ink-300"></span>
                                        <span>{{ $b->published_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-10">{{ $blogs->onEachSide(1)->links() }}</div>
            @else
                <div class="bg-white rounded-3xl border-2 border-dashed border-ink-200 p-12 text-center">
                    <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
                        <i class="fa-regular fa-newspaper text-2xl"></i>
                    </span>
                    <p class="font-display font-bold text-ink-900 mt-5">No hay artículos con esos criterios</p>
                    <p class="text-sm text-ink-500 mt-1">Prueba quitando algún filtro o búsqueda.</p>
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 mt-6 px-4 py-2.5 rounded-full bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">
                        Ver todos los artículos
                    </a>
                </div>
            @endif
        </div>

        {{-- ═══════════════ SIDEBAR ═══════════════ --}}
        <aside class="lg:sticky lg:top-24 space-y-5">

            {{-- Categorías --}}
            @if (! empty($categories))
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-400 mb-4">Categorías</h3>
                    <div class="space-y-1.5">
                        <a href="{{ route('blog.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl text-sm font-medium transition
                                  {{ ! request('category') ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                            Todas
                            <span class="text-xs text-ink-400">{{ collect($categories)->sum('blogs_count') }}</span>
                        </a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('blog.index', ['category' => $cat['slug']]) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-2xl text-sm font-medium transition
                                      {{ request('category') === $cat['slug'] ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background: {{ $cat['color'] }}"></span>
                                    {{ $cat['name'] }}
                                </span>
                                <span class="text-xs text-ink-400">{{ $cat['blogs_count'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- CTA newsletter --}}
            <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-6 shadow-lift relative overflow-hidden">
                <div class="blob bg-sun-300/40 w-44 h-44 -top-10 -right-10"></div>
                <div class="relative">
                    <i class="fa-solid fa-envelope-open text-sun-300 text-xl"></i>
                    <h3 class="font-display font-extrabold text-xl mt-3 leading-tight">Recibe artículos en tu correo</h3>
                    <p class="text-brand-50/90 text-sm mt-2">Cada semana, gratis. Sin spam.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-4 space-y-2">
                        @csrf
                        <input type="email" name="email" required placeholder="tu@correo.com"
                            class="w-full px-4 py-2.5 rounded-full bg-white/95 text-ink-900 placeholder-ink-400 text-sm border-0 focus:outline-none focus:ring-2 focus:ring-white/40">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-full font-bold bg-ink-900 text-white hover:bg-ink-700 text-sm transition">
                            Suscribirme <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection
