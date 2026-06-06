<!DOCTYPE html>
<html lang="{{ $generalSetting->default_locale ?? 'es' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = $generalSetting->site_name ?? 'Cursalia';
        $title    = trim(View::yieldContent('title')) ?: ($generalSetting->site_slogan ?? 'Aprende algo nuevo, a tu manera');
        $description = trim(View::yieldContent('description')) ?: ($generalSetting->seo_default_description ?? 'Cursalia · Plataforma de cursos online.');
        $ogImage  = trim(View::yieldContent('og-image')) ?: ($generalSetting->og_image ? asset('storage/'.$generalSetting->og_image) : asset('storage/og-default.png'));
        $currentUrl = url()->current();
        $favicon  = $generalSetting->favicon ? asset('storage/'.$generalSetting->favicon) : null;
        $themeColor = $generalSetting->brand_color ?? '#10B981';
    @endphp

    <title>{{ $title }} · {{ $siteName }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $currentUrl }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="{{ $themeColor }}">

    {{-- Verificación de propiedad en motores de búsqueda --}}
    @if (!empty($generalSetting->google_site_verification))
        <meta name="google-site-verification" content="{{ $generalSetting->google_site_verification }}">
    @endif
    @if (!empty($generalSetting->bing_site_verification))
        <meta name="msvalidate.01" content="{{ $generalSetting->bing_site_verification }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $title }} · {{ $siteName }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ $generalSetting->default_locale ?? 'es' }}_{{ strtoupper($generalSetting->default_locale ?? 'ES') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }} · {{ $siteName }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Tema dinámico — sobrescribe los tokens de Tailwind con las variables CSS de la marca --}}
    @include('partials.theme-vars')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">

    @if ($favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='{{ urlencode($themeColor) }}'><path d='M4 7l8-4 8 4-8 4-8-4z'/><path d='M4 7v6l8 4 8-4V7' fill='none' stroke='{{ urlencode($themeColor) }}' stroke-width='2'/></svg>">
    @endif

    {{-- JSON-LD base (sitio) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context'    => 'https://schema.org',
        '@type'       => 'EducationalOrganization',
        'name'        => $siteName,
        'url'         => url('/'),
        'description' => $description,
        'sameAs'      => collect($socialLinks ?? [])->pluck('url')->filter()->values()->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD adicional por página (Article, Course, FAQPage, BreadcrumbList…) --}}
    @stack('head')
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen flex flex-col" x-data="{ mobileMenu: false }">

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[60] focus:px-4 focus:py-2 focus:rounded-full focus:bg-brand-600 focus:text-white focus:font-bold focus:shadow-lift">
        Saltar al contenido
    </a>

    @include('partials.header')

    <main id="main" class="flex-1 pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Google Analytics 4 (solo en producción y si está configurado) --}}
    @if (app()->environment('production') && !empty($generalSetting->google_analytics_id))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $generalSetting->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $generalSetting->google_analytics_id }}', { anonymize_ip: true });
        </script>
    @endif

</body>
</html>
