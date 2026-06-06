@extends('layouts.app')

@section('title', $blog->seo_title)
@section('description', $blog->seo_description)
@if ($blog->og_image_url)
    @section('og-image', $blog->og_image_url)
@endif

{{-- ════════════════════ Schema.org por post ════════════════════ --}}
@push('head')
@php
    $author      = $blog->author;
    $publisher   = $generalSetting->site_name ?? 'Cursalia';
    $logoUrl     = !empty($generalSetting->logo)
                    ? asset('storage/'.$generalSetting->logo)
                    : asset('storage/og-default.png');
    $postUrl     = url('/blog/'.$blog->slug);
    $imageUrl    = $blog->og_image_url ?: asset('storage/og-default.png');

    // ─── BreadcrumbList ─────────────────────────────────────────────
    $breadcrumbItems = [
        ['name' => 'Inicio', 'url' => url('/')],
        ['name' => 'Blog',   'url' => url('/blog')],
    ];
    if ($blog->category) {
        $breadcrumbItems[] = [
            'name' => $blog->category->name,
            'url'  => url('/blog?category='.$blog->category->slug),
        ];
    }
    $breadcrumbItems[] = ['name' => $blog->title, 'url' => $postUrl];

    $breadcrumbLd = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => collect($breadcrumbItems)->map(fn ($i, $idx) => [
            '@type'    => 'ListItem',
            'position' => $idx + 1,
            'name'     => $i['name'],
            'item'     => $i['url'],
        ])->values()->all(),
    ];

    // ─── Person (autor) ─────────────────────────────────────────────
    $personLd = [
        '@type' => 'Person',
        '@id'   => url('/sobre-el-autor').'#person',
        'name'  => $author->name,
        'url'   => url('/sobre-el-autor'),
    ];
    if ($author->avatar_url) {
        $personLd['image'] = $author->avatar_url;
    }
    if ($author->headline) {
        $personLd['jobTitle'] = $author->headline;
    }
    if ($author->bio) {
        $personLd['description'] = \Illuminate\Support\Str::limit(strip_tags($author->bio), 500);
    }
    $same = $author->sameAs();
    if (!empty($same)) {
        $personLd['sameAs'] = $same;
    }

    // ─── Article ────────────────────────────────────────────────────
    $articleLd = [
        '@context'      => 'https://schema.org',
        '@type'         => $blog->isCourseLesson() ? 'LearningResource' : 'Article',
        'headline'      => $blog->seo_title,
        'description'   => $blog->seo_description,
        'image'         => [$imageUrl],
        'datePublished' => optional($blog->published_at)->toAtomString(),
        'dateModified'  => $blog->updated_at?->toAtomString(),
        'inLanguage'    => 'es',
        'url'           => $postUrl,
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id'   => $postUrl,
        ],
        'author'    => $personLd,
        'publisher' => [
            '@type' => 'Organization',
            'name'  => $publisher,
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => $logoUrl,
            ],
        ],
        'articleSection' => $blog->category?->name,
        'wordCount'      => str_word_count(strip_tags($blog->content)),
    ];

    // Limpiar nulls de primer nivel.
    $articleLd = array_filter($articleLd, fn ($v) => $v !== null && $v !== '');

    // Si es lección del curso, añadir referencias educativas.
    if ($blog->isCourseLesson()) {
        $articleLd['learningResourceType'] = 'Lesson';
        $articleLd['educationalLevel']     = 'Beginner';
        $articleLd['isPartOf'] = [
            '@type' => 'Course',
            '@id'   => url('/blog?category=curso-cursalia').'#course',
            'name'  => 'Curso Cursalia — construye tu academia online',
        ];
    }

    // ─── FAQPage (si hay) ───────────────────────────────────────────
    $faqLd = null;
    if (!empty($blog->faq) && is_array($blog->faq)) {
        $faqLd = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => collect($blog->faq)->map(fn ($item) => [
                '@type'          => 'Question',
                'name'           => $item['q'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['a'] ?? '',
                ],
            ])->values()->all(),
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode($articleLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@if ($faqLd)
<script type="application/ld+json">
{!! json_encode($faqLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endpush

@php
    // ─── Pre-procesamiento del contenido ────────────────────────────────────
    // 1. Sanitizar con Purifier.
    // 2. Añadir IDs a h2/h3 para que el TOC pueda hacer scroll a ellos.
    // 3. Extraer la lista de h2/h3 con sus IDs para el TOC sticky.
    $sanitizedHtml = \Mews\Purifier\Facades\Purifier::clean($blog->content, 'richtext');

    $headings = [];
    $slugifyHeading = function (string $text) use (&$headings): string {
        $slug = \Illuminate\Support\Str::slug(strip_tags($text));
        $base = $slug;
        $i = 2;
        $existingIds = array_column($headings, 'id');
        while (in_array($slug, $existingIds, true)) {
            $slug = $base.'-'.($i++);
        }
        return $slug;
    };

    $processedHtml = preg_replace_callback(
        '/<(h[23])(\s[^>]*)?>(.*?)<\/\1>/is',
        function ($m) use (&$headings, $slugifyHeading) {
            $tag = strtolower($m[1]);
            $attrs = $m[2] ?? '';
            $inner = $m[3];

            // Si ya tiene id, lo respetamos.
            if (preg_match('/\bid\s*=\s*"([^"]+)"/i', $attrs, $idm)) {
                $id = $idm[1];
            } else {
                $id = $slugifyHeading($inner);
                $attrs = ($attrs ?: '').' id="'.$id.'"';
            }
            $headings[] = ['level' => $tag, 'id' => $id, 'text' => strip_tags($inner)];
            return '<'.$tag.$attrs.'>'.$inner.'</'.$tag.'>';
        },
        $sanitizedHtml
    );

    $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags($blog->content)) / 200));
    $shareUrl = urlencode(request()->fullUrl());
    $shareTitle = urlencode($blog->title);
@endphp

@section('content')

{{-- Barra de progreso de lectura --}}
<div class="read-progress" id="read-progress"></div>

{{-- ═══════════════════════════════════════════════════════════════════
     HERO DEL ARTÍCULO
     ═══════════════════════════════════════════════════════════════════ --}}
<article x-data="{ shareCopied: false }">
<header class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[26rem] h-[26rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[22rem] h-[22rem] top-32 -right-10"></div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-ink-500 mb-6">
            <a href="{{ url('/') }}" class="hover:text-brand-700">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-700">Blog</a>
            @if ($blog->category)
                <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
                <a href="{{ route('blog.index', ['category' => $blog->category->slug]) }}" class="hover:text-brand-700">{{ $blog->category->name }}</a>
            @endif
        </nav>

        {{-- Categoría --}}
        @if ($blog->category)
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-[0.16em]"
                  style="background:{{ $blog->category->color }}1A; color:{{ $blog->category->color }}">
                <i class="fa-solid fa-bookmark text-[9px]"></i>
                {{ $blog->category->name }}
            </span>
        @endif

        {{-- Título grande --}}
        <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-[1.08] mt-6 text-ink-900">
            {{ $blog->title }}
        </h1>

        @if ($blog->summary)
            <p class="text-ink-500 text-lg sm:text-xl leading-relaxed mt-5">{{ $blog->summary }}</p>
        @endif

        {{-- Meta del artículo (autor + fecha + lectura) --}}
        <div class="flex items-center gap-4 mt-8 pb-8 border-b border-ink-200/70 flex-wrap">
            <a href="{{ route('author') }}" rel="author" class="flex items-center gap-3 group">
                @if ($blog->author?->image)
                    <img loading="lazy" src="{{ asset('storage/'.$blog->author->image) }}" alt="{{ $blog->author->name }}"
                         class="w-11 h-11 rounded-full object-cover ring-2 ring-brand-200 group-hover:ring-brand-400 transition">
                @else
                    <span class="grid place-items-center w-11 h-11 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold">
                        {{ strtoupper(substr($blog->author?->name ?? 'C', 0, 1)) }}
                    </span>
                @endif
                <div class="leading-tight">
                    <p class="font-display font-bold text-ink-900 text-sm group-hover:text-brand-700 transition">{{ $blog->author?->name ?? 'Equipo Cursalia' }}</p>
                    <p class="text-xs text-ink-500">
                        <i class="fa-regular fa-calendar text-[10px]"></i>
                        {{ $blog->published_at?->translatedFormat('d \d\e F, Y') }}
                        <span class="mx-1.5">·</span>
                        <i class="fa-regular fa-clock text-[10px]"></i>
                        {{ $readingMinutes }} min de lectura
                    </p>
                </div>
            </a>
            <div class="flex-1"></div>
            {{-- Share botones rápidos en el hero --}}
            <div class="hidden sm:flex items-center gap-1.5">
                <span class="text-xs font-semibold text-ink-500 mr-2">Compartir:</span>
                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener"
                   class="grid place-items-center w-9 h-9 rounded-full bg-cream-2 text-ink-700 hover:bg-ink-900 hover:text-white transition" aria-label="Twitter/X">
                    <i class="fa-brands fa-x-twitter text-sm"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
                   class="grid place-items-center w-9 h-9 rounded-full bg-cream-2 text-ink-700 hover:bg-[#0a66c2] hover:text-white transition" aria-label="LinkedIn">
                    <i class="fa-brands fa-linkedin-in text-sm"></i>
                </a>
                <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener"
                   class="grid place-items-center w-9 h-9 rounded-full bg-cream-2 text-ink-700 hover:bg-[#25d366] hover:text-white transition" aria-label="WhatsApp">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                </a>
            </div>
        </div>
    </div>
</header>

{{-- ═══════════════════════════════════════════════════════════════════
     IMAGEN DESTACADA
     ═══════════════════════════════════════════════════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2">
    @if ($blog->thumbnail)
        <img src="{{ asset('storage/'.$blog->thumbnail) }}" alt="{{ $blog->title }}"
             class="w-full aspect-[1200/630] {{ $blog->thumbnail_fit_class }} {{ $blog->thumbnail_is_svg ? 'bg-white' : '' }} rounded-3xl shadow-lift border border-ink-200/70">
    @else
        <div class="aspect-[1200/630] rounded-3xl shadow-lift border border-ink-200/70 bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700 grid place-items-center relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-sun-300/30 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-coral-300/30 blur-3xl"></div>
            <span class="relative font-display font-extrabold text-7xl sm:text-8xl text-white/90 select-none">
                {{ strtoupper(substr($blog->title, 0, 2)) }}
            </span>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     CONTENIDO + TOC SIDEBAR
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid lg:grid-cols-[1fr_240px] gap-12 items-start">

        {{-- Contenido del artículo --}}
        <div class="article-prose max-w-2xl mx-auto lg:mx-0" id="article-content">
            {!! $processedHtml !!}
        </div>

        {{-- Sidebar: TOC + Share + Newsletter --}}
        <aside class="hidden lg:block lg:sticky lg:top-24 space-y-6">
            {{-- Tabla de contenidos --}}
            @if (count($headings) > 1)
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-ink-400 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-list-ul text-brand-600"></i> En esta lección
                    </p>
                    <nav id="toc-nav" class="space-y-0.5">
                        @foreach ($headings as $h)
                            <a href="#{{ $h['id'] }}"
                               class="toc-link {{ $h['level'] === 'h3' ? 'toc-h3' : '' }}"
                               data-target="{{ $h['id'] }}">{{ $h['text'] }}</a>
                        @endforeach
                    </nav>
                </div>
            @endif

            {{-- Compartir vertical --}}
            <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-ink-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-share-nodes text-coral-500"></i> Comparte
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener"
                       class="grid place-items-center w-10 h-10 rounded-full bg-cream-2 text-ink-700 hover:bg-ink-900 hover:text-white transition">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener"
                       class="grid place-items-center w-10 h-10 rounded-full bg-cream-2 text-ink-700 hover:bg-[#0a66c2] hover:text-white transition">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener"
                       class="grid place-items-center w-10 h-10 rounded-full bg-cream-2 text-ink-700 hover:bg-[#1877f2] hover:text-white transition">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener"
                       class="grid place-items-center w-10 h-10 rounded-full bg-cream-2 text-ink-700 hover:bg-[#25d366] hover:text-white transition">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <button type="button" @click="navigator.clipboard.writeText(window.location.href); shareCopied = true; setTimeout(() => shareCopied = false, 2000)"
                            class="grid place-items-center w-10 h-10 rounded-full bg-cream-2 text-ink-700 hover:bg-brand-600 hover:text-white transition relative" title="Copiar link">
                        <i class="fa-regular fa-copy" x-show="!shareCopied"></i>
                        <i class="fa-solid fa-check" x-show="shareCopied" x-cloak></i>
                        <span x-show="shareCopied" x-cloak class="absolute -top-9 right-0 bg-ink-900 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap">¡Copiado!</span>
                    </button>
                </div>
            </div>

            {{-- Mini CTA newsletter --}}
            <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-5 shadow-lift relative overflow-hidden">
                <div class="blob bg-sun-300/40 w-32 h-32 -top-6 -right-6"></div>
                <div class="relative">
                    <i class="fa-solid fa-envelope-open-text text-sun-300 text-lg"></i>
                    <p class="font-display font-extrabold text-base mt-2 leading-tight">Recibe el Pack de inicio</p>
                    <p class="text-xs text-brand-50/85 mt-1.5">Cursalia FREE + checklist + las lecciones por email.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-3 space-y-1.5">
                        @csrf
                        <input type="email" name="email" required placeholder="tu@correo.com"
                            class="w-full px-3 py-2 rounded-full bg-white/95 text-ink-900 placeholder-ink-400 text-xs border-0 focus:outline-none focus:ring-2 focus:ring-white/40">
                        <button type="submit" class="w-full px-3 py-2 rounded-full font-bold bg-ink-900 text-white hover:bg-ink-700 text-xs transition">
                            Descargar
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     NAVEGACIÓN DEL CURSO (solo si es una lección del Curso Cursalia)
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($blog->isCourseLesson())
    @php
        $lessonNumber = $blog->getLessonNumber();
        $totalLessons = \App\Models\Blog::COURSE_FREE_TOTAL;
        $prev = $blog->previousLesson();
        $next = $blog->nextLesson();
        // Lección 0 = bienvenida (no es progreso de curso aún), Lección 14 = 100%
        $progress = $totalLessons > 0 ? min(100, max(0, ($lessonNumber / $totalLessons) * 100)) : 0;
        $progressMessage = match (true) {
            $lessonNumber === 0      => 'Acabas de empezar — ¡bienvenido al curso! 👋',
            $lessonNumber === $totalLessons => '¡Has terminado la Fase 1! 🎉',
            $progress >= 75          => 'Vas en la recta final · '.round($progress).'% completado 💪',
            $progress >= 25          => 'Llevas un buen ritmo · '.round($progress).'% completado',
            default                  => 'Has avanzado '.round($progress).'% del curso',
        };
    @endphp

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
        <div class="bg-white rounded-3xl border border-ink-200/70 shadow-lift p-6 sm:p-8">

            {{-- Encabezado: progreso del curso --}}
            <div class="flex items-center justify-between gap-3 mb-5 pb-5 border-b border-ink-200/70">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-700 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap"></i> Curso Cursalia · Fase 1
                    </p>
                    <p class="font-display font-extrabold text-ink-900 mt-1">
                        Lección <span class="text-brand-600">{{ $lessonNumber }}</span> de {{ $totalLessons }}
                    </p>
                </div>
                <a href="{{ route('blog.index', ['category' => $blog->category->slug]) }}"
                   class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-600">
                    Ver el índice del curso <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            {{-- Barra de progreso --}}
            <div class="relative h-2 rounded-full bg-cream-2 overflow-hidden mb-2">
                <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-brand-500 to-brand-700 transition-all"
                     style="width: {{ $progress }}%"></div>
            </div>
            <p class="text-xs text-ink-500 mb-7">{{ $progressMessage }}</p>

            {{-- Cards anterior / siguiente --}}
            <div class="grid sm:grid-cols-2 gap-4">

                {{-- ANTERIOR --}}
                @if ($prev)
                    <a href="{{ route('blog.show', $prev->slug) }}"
                       class="group p-5 rounded-2xl border-2 border-ink-200 hover:border-brand-300 bg-cream-2 hover:bg-brand-50 transition flex items-start gap-4">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-white text-ink-500 group-hover:bg-brand-600 group-hover:text-white transition shrink-0">
                            <i class="fa-solid fa-arrow-left"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400">Lección anterior · {{ $prev->getLessonNumber() }}</p>
                            <p class="font-display font-bold text-ink-900 text-sm leading-snug mt-1 line-clamp-2 group-hover:text-brand-700 transition">
                                {{ $prev->title }}
                            </p>
                        </div>
                    </a>
                @else
                    <div class="p-5 rounded-2xl border-2 border-dashed border-ink-200 bg-cream-2/50 flex items-center gap-3 text-ink-400">
                        <i class="fa-solid fa-flag-checkered"></i>
                        <p class="text-sm font-medium">Esta es la primera lección</p>
                    </div>
                @endif

                {{-- SIGUIENTE --}}
                @if ($next)
                    <a href="{{ route('blog.show', $next->slug) }}"
                       class="group p-5 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-soft hover:shadow-lift transition flex items-start gap-4 sm:flex-row-reverse sm:text-right">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-white/15 backdrop-blur shrink-0 group-hover:bg-white/30 transition">
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-brand-100">Próxima lección · {{ $next->getLessonNumber() }}</p>
                            <p class="font-display font-bold text-white text-sm leading-snug mt-1 line-clamp-2">
                                {{ $next->title }}
                            </p>
                        </div>
                    </a>
                @else
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-sun-300 to-coral-300 text-ink-900 flex items-center gap-3">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-white/40 backdrop-blur shrink-0">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </span>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider">Próximamente</p>
                            <p class="font-display font-bold text-sm leading-snug mt-1">
                                La siguiente lección se publica los viernes a las 9:00. Suscríbete arriba para recibirla por correo.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     CTA del autor + share final
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="rounded-3xl bg-gradient-to-br from-brand-500 via-brand-600 to-brand-700 text-white p-6 sm:p-10 shadow-lift relative overflow-hidden">
        <div class="blob bg-sun-300/40 w-80 h-80 -top-20 -right-20"></div>
        <div class="blob bg-coral-300/30 w-72 h-72 -bottom-20 -left-20"></div>
        <div class="relative grid sm:grid-cols-[auto_1fr_auto] items-center gap-6">
            <span class="grid place-items-center w-16 h-16 rounded-3xl bg-white/15 backdrop-blur text-sun-300">
                <i class="fa-solid fa-gift text-2xl"></i>
            </span>
            <div>
                <p class="font-display font-extrabold text-xl sm:text-2xl leading-tight">¿Te gustó esta lección?</p>
                <p class="text-brand-50/90 mt-1.5 text-sm">Descarga el Pack de inicio (Cursalia FREE + checklist + las próximas lecciones por email).</p>
            </div>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex gap-2 sm:flex-col">
                @csrf
                <input type="email" name="email" required placeholder="tu@correo.com"
                    class="flex-1 px-4 py-3 rounded-full bg-white/95 text-ink-900 placeholder-ink-400 text-sm border-0 focus:outline-none focus:ring-4 focus:ring-white/30 sm:min-w-[220px]">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full font-bold bg-ink-900 text-white hover:bg-ink-700 text-sm transition whitespace-nowrap">
                    Descargar gratis <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     COMENTARIOS
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6 sm:p-8">
        <h2 class="font-display font-extrabold text-2xl text-ink-900 flex items-center gap-2">
            <i class="fa-regular fa-comments text-brand-600 text-lg"></i>
            Comentarios <span class="text-ink-400 text-base font-normal">({{ $approvedComments->total() }})</span>
        </h2>

        @if ($approvedComments->isNotEmpty())
            <ul class="mt-6 space-y-6">
                @foreach ($approvedComments as $c)
                    <li class="flex gap-4">
                        <span class="grid place-items-center w-11 h-11 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold shrink-0">
                            {{ strtoupper(substr($c->name, 0, 1)) }}
                        </span>
                        <div class="flex-1">
                            <div class="flex items-baseline gap-2 flex-wrap">
                                <p class="font-display font-bold text-ink-900">{{ $c->name }}</p>
                                <span class="text-xs text-ink-400">{{ $c->approved_at?->diffForHumans() ?? $c->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-ink-700 leading-relaxed mt-1.5">{{ $c->comment }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            @if ($approvedComments->hasPages())<div class="mt-6">{{ $approvedComments->links() }}</div>@endif
        @else
            <p class="text-sm text-ink-500 mt-5">Aún no hay comentarios. ¡Sé el primero!</p>
        @endif

        <div class="mt-8 pt-8 border-t border-ink-200/70">
            <h3 class="font-display font-bold text-ink-900 text-lg">Deja tu comentario</h3>
            <p class="text-sm text-ink-500 mt-1">Los comentarios pasan por una moderación rápida.</p>
            @if (session('success'))
                <div class="mt-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-start gap-2">
                    <i class="fa-solid fa-circle-check mt-0.5"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            <form method="POST" action="{{ route('blog.comments.store', $blog->id) }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" placeholder="Tu nombre"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" placeholder="Tu correo (no se publica)"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <textarea name="comment" rows="4" required minlength="5" maxlength="1000" placeholder="Escribe tu comentario…"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-none">{{ old('comment') }}</textarea>

                <x-math-captcha label="Para evitar spam, demuéstranos que eres humano:" />

                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    Publicar <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     POSTS RELACIONADOS
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($recentBlogs->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-ink-200/60">
    <h2 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight text-ink-900 mb-8">Sigue leyendo</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ($recentBlogs as $r)
            <a href="{{ route('blog.show', $r->slug) }}" class="card-lift group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                <div class="relative aspect-video {{ $r->thumbnail_is_svg ? 'bg-white' : 'bg-gradient-to-br from-coral-300 to-sun-400' }}">
                    @if ($r->thumbnail)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$r->thumbnail) }}" alt="{{ $r->title }}" class="absolute inset-0 w-full h-full {{ $r->thumbnail_fit_class }}">
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    @if ($r->category)
                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: {{ $r->category->color }}">{{ $r->category->name }}</span>
                    @endif
                    <h3 class="font-display font-bold text-ink-900 leading-snug line-clamp-2 mt-1 group-hover:text-brand-700 transition text-sm">{{ $r->title }}</h3>
                    <p class="text-xs text-ink-500 mt-auto pt-3">{{ $r->published_at?->diffForHumans() }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

</article>

{{-- ═══════════════════════════════════════════════════════════════════
     Prism.js (syntax highlighting) cargado solo en blog show
     ═══════════════════════════════════════════════════════════════════ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js" integrity="sha512-/mZ1FHPkg6EKcxo0fKXF51ak6Cr2ocgDi5ytaTBjsQZIH/RNs6GF6+oId/vPe3eJB836T36nXwVh/WBl/cWT4w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js" referrerpolicy="no-referrer"></script>

{{-- ═══════════════════════════════════════════════════════════════════
     JS — barra de progreso + scroll spy TOC + wrap code blocks
     ═══════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    // 0) Envolver cada <pre><code class="language-XXX"> en un .code-block
    //    con header (lenguaje + botón copiar). Antes de que Prism procese.
    document.querySelectorAll('#article-content pre').forEach((pre) => {
        if (pre.parentElement?.classList?.contains('code-block')) return;
        const code = pre.querySelector('code');
        let lang = 'code';
        if (code) {
            const cls = [...code.classList].find((c) => c.startsWith('language-'));
            if (cls) lang = cls.replace('language-', '');
        } else {
            // Si no hay <code>, lo creamos para que Prism pueda procesarlo
            const raw = pre.innerHTML;
            const c = document.createElement('code');
            c.className = 'language-' + lang;
            c.innerHTML = raw;
            pre.innerHTML = '';
            pre.appendChild(c);
        }
        const wrap = document.createElement('div');
        wrap.className = 'code-block';
        wrap.innerHTML = `
            <div class="code-header">
                <span class="code-lang">${lang}</span>
                <button type="button" class="code-copy" title="Copiar código">
                    <i class="fa-regular fa-copy"></i>
                    <span>Copiar</span>
                </button>
            </div>
        `;
        pre.parentNode.insertBefore(wrap, pre);
        wrap.appendChild(pre);

        // Botón copiar con feedback
        wrap.querySelector('.code-copy').addEventListener('click', (e) => {
            const btn = e.currentTarget;
            const text = pre.querySelector('code')?.innerText ?? pre.innerText;
            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('copied');
                btn.querySelector('span').textContent = '¡Copiado!';
                btn.querySelector('i').className = 'fa-solid fa-check';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.querySelector('span').textContent = 'Copiar';
                    btn.querySelector('i').className = 'fa-regular fa-copy';
                }, 2000);
            });
        });
    });

    // Re-highlight con Prism por si el DOM ya estaba renderizado
    if (window.Prism) Prism.highlightAll();

    // 1) Barra de progreso de lectura
    const bar = document.getElementById('read-progress');
    const article = document.getElementById('article-content');
    if (! bar || ! article) return;

    const updateProgress = () => {
        const rect = article.getBoundingClientRect();
        const viewportH = window.innerHeight;
        const total = rect.height - viewportH + 200;
        const scrolled = Math.min(Math.max(-rect.top + viewportH * 0.2, 0), total);
        const percent = total > 0 ? (scrolled / total) * 100 : 0;
        bar.style.width = Math.min(100, Math.max(0, percent)) + '%';
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // 2) Scroll spy del TOC
    const tocLinks = document.querySelectorAll('#toc-nav .toc-link');
    if (tocLinks.length === 0) return;

    const targets = Array.from(tocLinks).map((a) => ({
        link: a,
        target: document.getElementById(a.dataset.target),
    })).filter((t) => t.target);

    const highlightActive = () => {
        let active = targets[0];
        const offset = 120;
        for (const t of targets) {
            if (t.target.getBoundingClientRect().top - offset <= 0) active = t;
        }
        tocLinks.forEach((a) => a.classList.remove('active'));
        if (active) active.link.classList.add('active');
    };
    window.addEventListener('scroll', highlightActive, { passive: true });
    highlightActive();

    // 3) Smooth scroll
    tocLinks.forEach((a) => {
        a.addEventListener('click', (e) => {
            const target = document.getElementById(a.dataset.target);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, '', '#' + a.dataset.target);
            }
        });
    });
})();
</script>

@endsection
