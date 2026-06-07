<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi panel') · Cursalia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310B981'><path d='M4 7l8-4 8 4-8 4-8-4z'/></svg>">
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen" x-data="{ sidebar: false }">

    {{-- Sidebar (desktop fijo / móvil deslizable) --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-ink-200/70 transform transition lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="h-full flex flex-col px-5 py-6">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
                </span>
                <span class="font-display font-extrabold text-xl tracking-tight text-ink-900">Cursalia</span>
            </a>

            {{-- Usuario --}}
            <div class="mt-7 p-4 rounded-2xl bg-cream-2 border border-ink-200/70 flex items-center gap-3">
                <span class="grid place-items-center w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-coral-400 text-white font-display font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-ink-900 truncate text-sm">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-ink-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            {{-- Nav --}}
            @php
                $nav = [
                    ['student.dashboard',             'fa-grid-2',          'Mi panel'],
                    ['student.enrolled-courses.index', 'fa-graduation-cap', 'Mis cursos'],
                    ['student.profile',               'fa-user',            'Mi perfil'],
                ];
            @endphp
            <nav class="mt-6 space-y-1 flex-1">
                @foreach ($nav as [$route, $icon, $label])
                    @php $active = request()->routeIs($route); @endphp
                    <a href="{{ route($route) }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-medium transition
                              {{ $active ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                        <i class="fa-solid {{ $icon }} w-5 text-center"></i>
                        <span>{{ $label }}</span>
                        @if ($active)<i class="fa-solid fa-circle text-[6px] text-brand-500 ml-auto"></i>@endif
                    </a>
                @endforeach
            </nav>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-ink-200/70">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-sm font-medium text-coral-500 hover:bg-coral-50 transition">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay móvil --}}
    <div x-show="sidebar" @click="sidebar = false" x-cloak class="fixed inset-0 bg-ink-950/40 z-30 lg:hidden"></div>

    {{-- Main --}}
    <div class="lg:ml-72 min-h-screen">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-cream/85 backdrop-blur-md border-b border-ink-200/70">
            <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3.5">
                <button @click="sidebar = !sidebar" class="lg:hidden grid place-items-center w-10 h-10 rounded-2xl hover:bg-cream-2 transition" aria-label="Menú">
                    <i class="fa-solid fa-bars text-ink-700"></i>
                </button>
                <h1 class="font-display font-bold text-ink-900 text-lg">@yield('page-title', 'Mi panel')</h1>
                <div class="hidden sm:flex items-center gap-2 text-sm text-ink-500">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ now()->translatedFormat('l, d \d\e F') }}</span>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>
    </div>

</body>
</html>
