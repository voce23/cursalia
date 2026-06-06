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

    {{-- Logo arriba --}}
    <header class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
            <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft group-hover:scale-105 transition">
                <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
            </span>
            <span class="font-display font-extrabold text-xl tracking-tight text-ink-900">Cursalia</span>
        </a>
    </header>

    {{-- Contenido --}}
    <main class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        @yield('content')
    </main>

</body>
</html>
