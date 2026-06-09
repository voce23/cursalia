<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · Cursalia Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310B981'><path d='M4 7l8-4 8 4-8 4-8-4z'/></svg>">
</head>
<body class="font-sans antialiased text-ink-900 bg-cream min-h-screen" x-data="{ sidebar: false }">

    {{-- ════════════════════ SIDEBAR (claro · paleta Cursalia) ════════════════════ --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-72 bg-white text-ink-700 border-r border-ink-200/70 shadow-soft transform transition lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="h-full flex flex-col">

            {{-- Marca · header con gradiente verde sutil --}}
            <div class="relative px-6 py-5 border-b border-ink-200/70 bg-gradient-to-br from-brand-50 via-white to-cream overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-brand-300/15 blur-2xl"></div>
                <a href="{{ route('admin.dashboard') }}" class="relative inline-flex items-center gap-2 group">
                    <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft group-hover:scale-105 transition">
                        <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
                    </span>
                    <div class="leading-tight">
                        <span class="block font-display font-extrabold text-lg tracking-tight text-ink-900">Cursalia</span>
                        <span class="block text-[10px] font-bold uppercase tracking-[0.18em] text-brand-700">Admin</span>
                    </div>
                </a>
            </div>

            {{-- Navegación agrupada --}}
            <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">
                @php
                    $admin = auth('admin')->user();
                    $groups = [
                        'General' => [
                            ['admin.dashboard', 'fa-house',     'Resumen',  false],
                        ],
                        'Aprendizaje' => [
                            ['admin.course-categories.index', 'fa-folder-tree', 'Categorías', false],
                            ['admin.courses.index', 'fa-book-open', 'Cursos',      false],
                            ['admin.quizzes.index', 'fa-circle-question', 'Autoevaluaciones', false],
                        ],
                        'Contenido' => [
                            ['admin.blogs.index',            'fa-newspaper',  'Artículos blog',    false],
                            ['admin.blog-categories.index',  'fa-tags',       'Categorías blog',   false],
                            ['admin.blog-comments.index',    'fa-comments',   'Comentarios',       false],
                            ['admin.messages.index',         'fa-envelope',   'Mensajes',          false],
                        ],
                        'Marketplace' => [
                            ['admin.templates.index',    'fa-boxes-stacked',    'Plantillas',          false],
                            ['admin.templates.waitlist', 'fa-bell',             'Lista de espera',     false],
                            ['admin.services.index',     'fa-handshake-angle',  'Servicios',           false],
                            ['admin.services.requests',  'fa-inbox',            'Pedidos de servicios', false],
                        ],
                        'Sistema' => [
                            ['admin.profile',         'fa-user-gear',    'Mi perfil',     false],
                            ['admin.appearance.edit', 'fa-paint-roller', 'Apariencia',    false],
                            ['admin.navigation.edit', 'fa-bars',         'Navegación',    false],
                        ],
                    ];
                @endphp

                @foreach ($groups as $label => $items)
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-ink-400 px-3 mb-2">{{ $label }}</p>
                        <div class="space-y-0.5">
                            @foreach ($items as [$route, $icon, $name, $soon])
                                @php $active = ! $soon && $route !== '#' && request()->routeIs($route); @endphp
                                <a href="{{ $soon ? '#' : route($route) }}"
                                   @if($soon) onclick="event.preventDefault()" @endif
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-medium transition
                                          {{ $active
                                             ? 'bg-brand-100 text-brand-700 font-semibold shadow-soft'
                                             : 'text-ink-700 hover:bg-cream-2 hover:text-ink-900' }}
                                          {{ $soon ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <i class="fa-solid {{ $icon }} w-5 text-center {{ $active ? 'text-brand-600' : 'text-ink-400' }}"></i>
                                    <span class="flex-1">{{ $name }}</span>
                                    @if ($soon)
                                        <span class="text-[9px] font-bold uppercase bg-ink-100 px-1.5 py-0.5 rounded-md text-ink-500">Pronto</span>
                                    @elseif ($active)
                                        <i class="fa-solid fa-circle text-[5px] text-brand-500"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            {{-- Card user / logout --}}
            <div class="p-3 border-t border-ink-200/70 bg-cream-2/50">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-2xl bg-white border border-ink-200/70">
                    <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold shrink-0 shadow-soft">
                        {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-ink-900 truncate">{{ $admin->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-ink-500 truncate">{{ $admin->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="grid place-items-center w-8 h-8 rounded-xl text-ink-400 hover:bg-coral-50 hover:text-coral-600 transition" title="Cerrar sesión">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Overlay móvil --}}
    <div x-show="sidebar" @click="sidebar = false" x-cloak class="fixed inset-0 bg-ink-950/60 z-30 lg:hidden"></div>

    {{-- ════════════════════ MAIN ════════════════════ --}}
    <div class="lg:ml-72 min-h-screen flex flex-col">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 bg-cream/85 backdrop-blur-md border-b border-ink-200/70">
            <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3.5 gap-3">
                <button @click="sidebar = !sidebar" class="lg:hidden grid place-items-center w-10 h-10 rounded-2xl hover:bg-cream-2 transition" aria-label="Menú">
                    <i class="fa-solid fa-bars text-ink-700"></i>
                </button>
                <div class="flex-1 min-w-0">
                    <h1 class="font-display font-bold text-ink-900 text-lg truncate">@yield('page-title', 'Resumen')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-xs text-ink-500 truncate hidden sm:block">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ url('/') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-full bg-white border border-ink-200 text-ink-700 text-sm font-semibold hover:bg-cream-2 transition" title="Ver el sitio">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        Ver sitio
                    </a>
                    <div class="hidden sm:flex items-center gap-2 text-xs text-ink-500">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-brand-50 text-brand-700 font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> FASE 1
                        </span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flashes --}}
        @if (session('success'))
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <div class="max-w-7xl mx-auto px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-start gap-2">
                    <i class="fa-solid fa-circle-check mt-0.5"></i>
                    <span class="flex-1">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <div class="max-w-7xl mx-auto px-4 py-3 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 text-sm flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span class="flex-1">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Contenido --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <footer class="px-4 sm:px-6 lg:px-8 py-5 text-xs text-ink-400 text-center">
            Cursalia Admin · construido con Laravel 13 · @ {{ date('Y') }}
        </footer>
    </div>
</body>
</html>
