{{-- ════════════════════════════════════════════════════════════════════
     HEADER · pill flotante · 100% white-label.
     Lee logo, marca y enlaces de $generalSetting + $headerLinks (DB).
     ════════════════════════════════════════════════════════════════════ --}}
@php
    $siteName = $generalSetting->site_name ?? 'Cursalia';
    $logoPath = $generalSetting->logo;
    $catLimit = (int) ($headerSetting->category_limit ?? 8);

    $headerCategories = \App\Models\CourseCategory::query()
        ->whereNull('parent_id')
        ->orderBy('name')
        ->take($catLimit)
        ->get(['name', 'slug']);
@endphp

<header class="fixed top-3 inset-x-0 z-50 px-3 sm:px-6">
    <div class="max-w-6xl mx-auto bg-white/85 backdrop-blur-md border border-ink-200/70 rounded-full shadow-soft px-4 sm:px-6 py-2.5 flex items-center justify-between gap-3">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
            @if ($logoPath)
                <img loading="lazy" decoding="async" src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}" class="h-9 w-auto">
            @else
                <span class="grid place-items-center w-9 h-9 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 7l8-4 8 4-8 4-8-4z"/>
                        <path d="M4 7v6l8 4 8-4V7"/>
                        <path d="M12 11v10"/>
                    </svg>
                </span>
                <span class="font-display font-extrabold text-lg tracking-tight text-ink-900">{{ $siteName }}</span>
            @endif
        </a>

        {{-- Links centro (desktop) — dinámicos desde DB --}}
        <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-ink-700" aria-label="Navegación principal">
            @foreach (($headerLinks ?? []) as $link)
                @php
                    $url = $link['url'];
                    $isActive = $url && $url !== '#' && request()->is(ltrim($url, '/').'*') && ltrim($url, '/') !== '';
                    $isCategories = strcasecmp($link['title'], $headerSetting->category_button_text ?? 'Categorías') === 0;
                @endphp

                @if ($isCategories)
                    {{-- Categorías como dropdown --}}
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button @click="open = !open"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full hover:bg-brand-50 hover:text-brand-700 transition">
                            {{ $link['title'] }}
                            <svg viewBox="0 0 24 24" class="w-3 h-3" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-72">
                            <div class="bg-white rounded-3xl border border-ink-200/70 shadow-lift p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400 px-3 py-2">Explora por área</p>
                                <div class="grid grid-cols-1 gap-0.5">
                                    @forelse ($headerCategories as $cat)
                                        <a href="{{ route('courses.index', ['category' => $cat->slug]) }}"
                                           class="flex items-center justify-between px-3 py-2 rounded-2xl hover:bg-brand-50 hover:text-brand-700 transition text-sm">
                                            <span class="flex items-center gap-2">
                                                <i class="fa-solid fa-tag text-[10px] text-brand-500"></i>
                                                {{ $cat->name }}
                                            </span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-ink-300"></i>
                                        </a>
                                    @empty
                                        <div class="px-3 py-6 text-center">
                                            <i class="fa-regular fa-folder-open text-2xl text-ink-300"></i>
                                            <p class="text-xs text-ink-400 mt-2">Aún no hay categorías.</p>
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('courses.index') }}" class="mt-2 inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-full bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 transition">
                                    Ver todos los cursos <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ $url }}"
                       @if (! empty($link['open_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                       class="px-3.5 py-2 rounded-full transition
                              {{ $isActive ? 'bg-brand-50 text-brand-700 font-semibold' : 'hover:bg-brand-50 hover:text-brand-700' }}"
                       @if ($isActive) aria-current="page" @endif>
                        {{ $link['title'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Derecha (desktop) --}}
        <div class="hidden lg:flex items-center gap-2 shrink-0">
            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="inline-flex items-center gap-2 pl-1 pr-3 py-1 rounded-full hover:bg-brand-50 transition">
                        @if (auth()->user()->image)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.auth()->user()->image) }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <span class="grid place-items-center w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        @endif
                        <span class="text-sm font-semibold text-ink-700 max-w-[100px] truncate">{{ explode(' ', auth()->user()->name)[0] }}</span>
                        <svg viewBox="0 0 24 24" class="w-3 h-3 text-ink-500" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-ink-200/70 shadow-lift p-2 z-10">
                        @php
                            $dashUrl = auth()->user()->role === 'instructor'
                                ? (auth()->user()->approve_status === 'approved' ? '/instructor/dashboard' : '/instructor/pending')
                                : route('student.dashboard');
                        @endphp
                        <a href="{{ $dashUrl }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-brand-50 hover:text-brand-700 transition text-sm">
                            <i class="fa-solid fa-grid-2 w-4 text-center"></i> Mi panel
                        </a>
                        @if (auth()->user()->role !== 'instructor')
                            <a href="{{ route('student.profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-brand-50 hover:text-brand-700 transition text-sm">
                                <i class="fa-solid fa-user w-4 text-center"></i> Mi perfil
                            </a>
                        @endif
                        <hr class="my-1.5 border-ink-200/70">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-coral-50 hover:text-coral-600 transition text-sm">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-ink-700 hover:text-brand-700 rounded-full transition">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-full bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    Crear cuenta
                    <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @endauth
        </div>

        {{-- Botón móvil --}}
        <button @click="mobileMenu = !mobileMenu" class="lg:hidden grid place-items-center w-10 h-10 rounded-full hover:bg-brand-50 transition" aria-label="Abrir menú">
            <svg x-show="!mobileMenu" viewBox="0 0 24 24" class="w-6 h-6 text-ink-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg x-show="mobileMenu" x-cloak viewBox="0 0 24 24" class="w-6 h-6 text-ink-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    {{-- Menú móvil --}}
    <div x-show="mobileMenu" x-cloak x-collapse class="lg:hidden max-w-6xl mx-auto mt-2 bg-white border border-ink-200/70 rounded-3xl shadow-lift overflow-hidden">
        <div class="px-4 py-4 space-y-1 text-sm font-medium">
            @foreach (($headerLinks ?? []) as $link)
                <a href="{{ $link['url'] }}"
                   @if (! empty($link['open_new_tab'])) target="_blank" rel="noopener noreferrer" @endif
                   class="block px-3 py-3 rounded-2xl hover:bg-brand-50 hover:text-brand-700 transition">
                    {{ $link['title'] }}
                </a>
            @endforeach
            <div class="pt-3 mt-2 border-t border-ink-200/70 flex flex-col gap-2">
                @auth
                    @php
                        $dashUrlM = auth()->user()->role === 'instructor'
                            ? (auth()->user()->approve_status === 'approved' ? '/instructor/dashboard' : '/instructor/pending')
                            : route('student.dashboard');
                    @endphp
                    <a href="{{ $dashUrlM }}" class="text-center px-4 py-3 rounded-full bg-brand-600 text-white font-semibold shadow-soft hover:bg-brand-700 transition">Mi panel</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-3 rounded-full border border-ink-200 hover:bg-cream-2 text-ink-700 transition">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-center px-4 py-3 rounded-full border border-ink-200 hover:bg-brand-50 hover:text-brand-700 transition">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="text-center px-4 py-3 rounded-full bg-brand-600 text-white font-semibold shadow-soft hover:bg-brand-700 transition">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </div>
</header>
