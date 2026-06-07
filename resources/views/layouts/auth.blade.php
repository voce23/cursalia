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
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen" x-data="{ mobileMenu: false }">

    {{-- Decoración orgánica de fondo --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob bg-brand-200 w-[32rem] h-[32rem] -top-32 -left-32"></div>
        <div class="blob bg-coral-200 w-[28rem] h-[28rem] -bottom-32 -right-32"></div>
        <div class="blob bg-sun-200 w-[24rem] h-[24rem] top-1/3 -right-20"></div>
    </div>

    {{-- MISMO header que el resto del sitio (consistencia total del menú) --}}
    @include('partials.header')

    {{-- Contenido. pt generoso para separar del header flotante (fixed top-3). --}}
    <main class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 sm:pt-32 pb-10 lg:pb-16">
        @yield('content')
    </main>

</body>
</html>
