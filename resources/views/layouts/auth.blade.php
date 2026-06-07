<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cursalia')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310B981'><path d='M4 7l8-4 8 4-8 4-8-4z'/></svg>">
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen">

    {{-- Decoración orgánica de fondo --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob bg-brand-200 w-[32rem] h-[32rem] -top-32 -left-32"></div>
        <div class="blob bg-coral-200 w-[28rem] h-[28rem] -bottom-32 -right-32"></div>
        <div class="blob bg-sun-200 w-[24rem] h-[24rem] top-1/3 -right-20"></div>
    </div>

    {{-- Header con logo + navegación (para no dejar al visitante atrapado) --}}
    <header class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 flex items-center justify-between gap-4">
        {{-- Logo → home --}}
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group shrink-0">
            @if (!empty($generalSetting->logo))
                <img src="{{ asset('storage/'.$generalSetting->logo) }}" alt="{{ $generalSetting->site_name ?? 'Cursalia' }}" class="h-9 w-auto">
            @else
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft group-hover:scale-105 transition">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
                </span>
                <span class="font-display font-extrabold text-xl tracking-tight text-ink-900">{{ $generalSetting->site_name ?? 'Cursalia' }}</span>
            @endif
        </a>

        {{-- Navegación: links principales (desktop) + "volver al inicio" (móvil) --}}
        <nav class="flex items-center gap-1 sm:gap-2">
            @if (!empty($headerLinks))
                <div class="hidden sm:flex items-center gap-1">
                    @foreach (collect($headerLinks)->take(5) as $link)
                        <a href="{{ $link['url'] }}"
                           @if(!empty($link['open_new_tab'])) target="_blank" rel="noopener" @endif
                           class="px-3 py-2 rounded-full text-sm font-medium text-ink-600 hover:text-brand-700 hover:bg-white/70 transition">
                            {{ $link['title'] }}
                        </a>
                    @endforeach
                </div>
            @endif
            {{-- En móvil: un acceso claro para volver --}}
            <a href="{{ url('/') }}" class="sm:hidden inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-sm font-semibold text-brand-700 hover:bg-white/70 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Inicio
            </a>
        </nav>
    </header>

    {{-- Contenido --}}
    <main class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        @yield('content')
    </main>

</body>
</html>
