<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · Cursalia</title>
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen relative overflow-hidden">
    <div class="blob bg-brand-200 w-[28rem] h-[28rem] -top-24 -left-24"></div>
    <div class="blob bg-coral-200 w-[24rem] h-[24rem] -bottom-24 -right-24"></div>

    <div class="relative min-h-screen flex items-center justify-center px-4 py-10">
        <div class="max-w-lg w-full text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-10">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
                </span>
                <span class="font-display font-extrabold text-xl tracking-tight">Cursalia</span>
            </a>

            <p class="font-display font-extrabold text-7xl sm:text-8xl text-transparent bg-gradient-to-br bg-clip-text @yield('grad','from-brand-500 to-brand-700')">@yield('code')</p>
            <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-ink-900 mt-4">@yield('heading')</h1>
            <p class="text-ink-500 mt-3 leading-relaxed">@yield('message')</p>

            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mt-8 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white shadow-soft hover:bg-brand-700 transition">
                <i class="fa-solid fa-house"></i> Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
