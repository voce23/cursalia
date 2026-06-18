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
        // og:type dinámico: cada página puede declarar 'article' (posts blog),
        // 'profile' (perfiles), 'product' (cursos), etc. Fallback "website".
        $ogType   = trim(View::yieldContent('og-type')) ?: 'website';
        $currentUrl = url()->current();
        // canonical: por defecto la URL actual; las páginas pueden overridear
        // (útil en filtros con query string para evitar duplicate content).
        $canonicalUrl = trim(View::yieldContent('canonical')) ?: $currentUrl;
        $favicon  = $generalSetting->favicon ? asset('storage/'.$generalSetting->favicon) : null;
        $themeColor = $generalSetting->brand_color ?? '#10B981';
    @endphp

    <title>{{ $title }} · {{ $siteName }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
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
    <meta property="og:type" content="{{ $ogType }}">
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
    {{-- Red de seguridad: si el usuario tiene JS desactivado, revelamos el
         contenido (.sr empieza invisible y normalmente lo revela el JS). --}}
    <noscript><style>.sr{opacity:1 !important;transform:none !important}</style></noscript>
    {{-- Font Awesome cargado de forma asíncrona (no bloquea LCP).
         preconnect: TCP handshake hecho antes; CSS asíncrono: hace "print" hasta que carga. --}}
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          media="print" onload="this.media='all'" referrerpolicy="no-referrer">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"></noscript>

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

    {{-- Toast global para flashes 'status' (ej. suscripción al newsletter).
         El layout público no renderiza toasts de php-flasher; 'status' no es
         interceptado por flasher, así que lo mostramos aquí para todo el sitio. --}}
    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition.opacity.duration.300ms
             class="fixed top-24 right-4 z-[70] max-w-sm px-5 py-4 rounded-2xl bg-brand-50 border-2 border-brand-300 text-brand-800 shadow-lift flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-brand-500 text-xl mt-0.5"></i>
            <p class="text-sm font-semibold flex-1">{{ session('status') }}</p>
            <button @click="show = false" class="text-brand-400 hover:text-brand-600" aria-label="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <main id="main" class="flex-1 pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- A8 · Cookie banner RGPD (UE) / LSSI (España) --}}
    <x-cookie-banner />

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

    {{-- Red de seguridad anti "página en blanco": si app.js no llegó a inicializar
         el scroll-reveal (p. ej. fallo de carga del bundle), revelamos todo el
         contenido pasados 1,5 s para que NUNCA quede invisible. --}}
    <script>
        setTimeout(function () {
            if (!window.__srReady) {
                document.querySelectorAll('.sr').forEach(function (el) { el.classList.add('in'); });
            }
        }, 1500);
    </script>

    {{-- Botón flotante de WhatsApp (complemento gratis activable desde Ajustes) --}}
    @include('partials.whatsapp-float')

</body>
</html>
