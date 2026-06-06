@extends('layouts.app')

@section('title', 'Sobre el autor · '.$author->name)
@section('description', $author->headline ?: 'Conoce al autor del blog y curso Cursalia: experiencia, redes oficiales y todos los artículos publicados.')

{{-- ════════════════════ Schema.org Person + ProfilePage + AboutPage ════════════════════ --}}
@push('head')
@php
    $personLd = array_filter([
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        '@id'         => url('/sobre-el-autor').'#person',
        'name'        => $author->name,
        'url'         => url('/sobre-el-autor'),
        'image'       => $author->avatar_url,
        'jobTitle'    => $author->headline,
        'description' => $author->bio ? \Illuminate\Support\Str::limit(strip_tags($author->bio), 500) : null,
        'sameAs'      => $author->sameAs() ?: null,
        'worksFor'    => [
            '@type' => 'Organization',
            'name'  => $generalSetting->site_name ?? 'Cursalia',
            'url'   => url('/'),
        ],
    ], fn ($v) => $v !== null && $v !== '');

    $profileLd = [
        '@context'   => 'https://schema.org',
        '@type'      => 'ProfilePage',
        'mainEntity' => $personLd,
        'inLanguage' => 'es',
        'url'        => url('/sobre-el-autor'),
    ];

    $breadcrumbLd = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio',     'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sobre el autor', 'item' => url('/sobre-el-autor')],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($profileLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')

{{-- HERO ─────────────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[26rem] h-[26rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[22rem] h-[22rem] top-32 -right-10"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-ink-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-brand-700">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
            <span class="text-ink-900 font-medium">Sobre el autor</span>
        </nav>

        <div class="grid sm:grid-cols-[180px_1fr] gap-8 items-start">
            {{-- Foto --}}
            <div class="text-center sm:text-left">
                @if ($author->image)
                    <x-image :src="$author->image"
                             :alt="'Foto de '.$author->name"
                             class="w-40 h-40 rounded-3xl shadow-lift border-4 border-white mx-auto sm:mx-0"
                             :width="200" :height="200"
                             eager />
                @else
                    <span class="grid place-items-center w-40 h-40 rounded-3xl bg-gradient-to-br from-brand-400 to-coral-400 text-white font-display font-extrabold text-5xl shadow-lift mx-auto sm:mx-0">
                        {{ strtoupper(substr($author->name, 0, 1)) }}
                    </span>
                @endif
            </div>

            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-pen-nib text-[10px]"></i> Autor del blog Cursalia
                </span>
                <h1 class="font-display font-extrabold text-4xl sm:text-5xl tracking-tight text-ink-900 mt-4">
                    {{ $author->name }}
                </h1>
                @if ($author->headline)
                    <p class="text-ink-600 text-lg mt-2 leading-relaxed">{{ $author->headline }}</p>
                @endif

                {{-- Redes oficiales --}}
                @php $same = $author->sameAs(); @endphp
                @if (!empty($same))
                    <div class="flex flex-wrap items-center gap-2 mt-5">
                        @php
                            $socialIcons = [
                                'social_x'        => ['fa-brands fa-x-twitter',  'X'],
                                'social_linkedin' => ['fa-brands fa-linkedin',   'LinkedIn'],
                                'social_github'   => ['fa-brands fa-github',     'GitHub'],
                                'social_youtube'  => ['fa-brands fa-youtube',    'YouTube'],
                                'social_web'      => ['fa-solid fa-globe',       'Web'],
                            ];
                        @endphp
                        @foreach ($socialIcons as $field => [$icon, $label])
                            @if ($author->$field)
                                <a href="{{ $author->$field }}" target="_blank" rel="noopener me"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700 text-sm font-semibold text-ink-700 transition shadow-soft">
                                    <i class="{{ $icon }} text-xs"></i>
                                    {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- BIO ─────────────────────────────────────────────────────────────── --}}
@if ($author->bio)
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-7 sm:p-10">
            <h2 class="font-display font-extrabold text-2xl text-ink-900 mb-4 flex items-center gap-3">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-brand-100 text-brand-700">
                    <i class="fa-solid fa-user-pen"></i>
                </span>
                Sobre mí
            </h2>
            <div class="prose-cursalia text-ink-700 leading-relaxed whitespace-pre-line">{{ $author->bio }}</div>
        </div>
    </section>
@endif

{{-- LECCIONES DEL CURSO ───────────────────────────────────────────── --}}
@if ($courseLessons->isNotEmpty())
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand-700">Curso gratis · 14 lecciones</p>
                <h2 class="font-display font-extrabold text-3xl text-ink-900 mt-1">Curso Cursalia</h2>
            </div>
            <a href="{{ route('blog.index', ['category' => \App\Models\Blog::COURSE_CATEGORY_SLUG]) }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-600">
                Ver todas <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($courseLessons as $lesson)
                <a href="{{ route('blog.show', $lesson->slug) }}"
                   class="block bg-white border border-ink-200/70 rounded-3xl shadow-soft hover:shadow-lift hover:border-brand-200 p-5 transition group">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-brand-700">Lección {{ str_pad((string) $lesson->getLessonNumber(), 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="font-display font-bold text-ink-900 mt-1 group-hover:text-brand-700 transition">{{ $lesson->title }}</p>
                    @if ($lesson->summary)
                        <p class="text-sm text-ink-600 mt-2 leading-relaxed line-clamp-2">{{ $lesson->summary }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- OTROS POSTS ────────────────────────────────────────────────────── --}}
@if ($otherPosts->isNotEmpty())
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="font-display font-extrabold text-3xl text-ink-900 mb-6">Otros artículos</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($otherPosts as $post)
                <a href="{{ route('blog.show', $post->slug) }}"
                   class="block bg-white border border-ink-200/70 rounded-3xl shadow-soft hover:shadow-lift hover:border-brand-200 p-5 transition group">
                    @if ($post->category)
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                              style="background:{{ $post->category->color }}1A; color:{{ $post->category->color }}">
                            {{ $post->category->name }}
                        </span>
                    @endif
                    <p class="font-display font-bold text-ink-900 mt-2 group-hover:text-brand-700 transition">{{ $post->title }}</p>
                    @if ($post->summary)
                        <p class="text-sm text-ink-600 mt-2 leading-relaxed line-clamp-2">{{ $post->summary }}</p>
                    @endif
                    <p class="text-xs text-ink-400 mt-3">{{ optional($post->published_at)->translatedFormat('j F Y') }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- CTA ─────────────────────────────────────────────────────────────── --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-gradient-to-br from-brand-600 to-brand-700 text-white rounded-3xl p-8 sm:p-10 text-center shadow-lift">
        <p class="font-display font-extrabold text-2xl sm:text-3xl">¿Quieres seguir el curso?</p>
        <p class="text-brand-100 mt-2 max-w-xl mx-auto">Cada viernes a las 9:00 publico una lección nueva. Suscríbete y la recibes por email.</p>
        <a href="{{ route('blog.index', ['category' => \App\Models\Blog::COURSE_CATEGORY_SLUG]) }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-brand-700 font-bold mt-6 hover:bg-brand-50 transition">
            <i class="fa-solid fa-graduation-cap"></i>
            Ver el curso completo
        </a>
    </div>
</section>

@endsection
