@extends('layouts.app')

@section('title', 'Marketplace de plantillas')
@section('description', 'Plantillas Cursalia listas para publicar tu próximo proyecto. LMS, paletas, componentes y packs profesionales.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO Marketplace
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[28rem] h-[28rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[24rem] h-[24rem] top-20 -right-10"></div>
    <div class="blob bg-sun-200 w-[20rem] h-[20rem] top-60 left-1/3"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10 text-center">
        <span class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 font-bold text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> {{ $templates->total() }} listas
            </span>
            Plantillas Cursalia
        </span>

        <h1 class="sr s1 font-display font-extrabold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-6 text-ink-900">
            Plantillas listas para <span class="text-brand-600">lanzar.</span>
        </h1>
        <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-5 max-w-2xl mx-auto">
            Templates Cursalia para tu academia, tu portafolio o tu próximo proyecto.
            Algunas son <strong class="text-brand-700">gratis para empezar</strong>; otras son nuestros productos premium.
        </p>

        {{-- Buscador --}}
        <form action="{{ route('templates.index') }}" method="GET" class="sr s3 relative max-w-xl mx-auto mt-8">
            @foreach (['category', 'price', 'sort'] as $hidden)
                @if (request($hidden))<input type="hidden" name="{{ $hidden }}" value="{{ request($hidden) }}">@endif
            @endforeach
            <div class="relative bg-white rounded-full border border-ink-200 shadow-lift flex items-center pl-5 pr-2 py-2 transition focus-within:ring-4 focus-within:ring-brand-100 focus-within:border-brand-400">
                <i class="fa-solid fa-magnifying-glass text-ink-400 mr-3"></i>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Busca por nombre o tema…"
                    class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-ink-900 placeholder-ink-400 text-sm sm:text-base py-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition text-sm">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     DESTACADOS (sólo página 1 sin filtros)
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($templates->currentPage() === 1 && ! request('search') && ! request('category') && ! request('price') && $featured->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-2">
    <h2 class="sr text-xs font-bold uppercase tracking-[0.2em] text-ink-400 mb-4">
        <i class="fa-solid fa-star text-sun-500"></i> Destacadas esta semana
    </h2>
    <div class="grid sm:grid-cols-3 gap-5">
        @foreach ($featured as $t)
            <a href="{{ route('templates.show', $t->slug) }}" class="card-lift sr s{{ $loop->iteration }} group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                <div class="relative aspect-[16/10] bg-gradient-to-br from-brand-400 to-brand-700">
                    @if ($t->thumbnail)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$t->thumbnail) }}" alt="{{ $t->title }}" class="absolute inset-0 w-full h-full object-cover">
                    @endif
                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 text-sun-600 text-[10px] font-bold uppercase tracking-wider shadow-soft">
                        <i class="fa-solid fa-star text-[9px]"></i> Destacado
                    </span>
                    @if ($t->is_free)
                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold shadow-soft">Gratis</span>
                    @elseif ($t->has_discount)
                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full bg-coral-500 text-white text-xs font-bold shadow-soft">-{{ $t->discount_percent }}%</span>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    @if ($t->category)
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider self-start"
                              style="color: {{ $t->category->color }}">{{ $t->category->name }}</span>
                    @endif
                    <h3 class="font-display font-bold text-ink-900 leading-snug mt-1 group-hover:text-brand-700 transition">{{ $t->title }}</h3>
                    @if ($t->headline)
                        <p class="text-xs text-ink-500 mt-1.5 line-clamp-2">{{ $t->headline }}</p>
                    @endif
                    <div class="mt-auto pt-3 flex items-baseline justify-between">
                        @if ($t->is_free)
                            <span class="font-display font-extrabold text-brand-600">Gratis</span>
                        @elseif ($t->has_discount)
                            <span class="font-display font-extrabold text-ink-900">${{ number_format($t->discount, 0) }}<span class="text-xs text-ink-400 line-through ml-1.5">${{ number_format($t->price, 0) }}</span></span>
                        @else
                            <span class="font-display font-extrabold text-ink-900">${{ number_format($t->price, 0) }}</span>
                        @endif
                        <i class="fa-solid fa-arrow-right text-xs text-ink-300 group-hover:text-brand-600 group-hover:translate-x-1 transition"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     LAYOUT CON SIDEBAR + GRID
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid lg:grid-cols-[260px_1fr] gap-8 items-start">

        {{-- ═══════════ SIDEBAR FILTROS ═══════════ --}}
        <aside class="lg:sticky lg:top-24 space-y-5">
            {{-- Categorías --}}
            <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-4">Categorías</h3>
                <div class="space-y-1">
                    <a href="{{ route('templates.index') }}"
                       class="flex items-center justify-between px-3 py-2 rounded-2xl text-sm font-medium transition
                              {{ ! request('category') ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                        Todas
                        <span class="text-xs text-ink-400">{{ $categories->sum('templates_count') }}</span>
                    </a>
                    @foreach ($categories as $cat)
                        <a href="{{ route('templates.index', ['category' => $cat->slug]) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl text-sm font-medium transition
                                  {{ request('category') === $cat->slug ? 'bg-brand-50 text-brand-700' : 'text-ink-700 hover:bg-cream-2' }}">
                            <span class="flex items-center gap-2">
                                @if ($cat->icon)<i class="{{ $cat->icon }} text-[12px]" style="color: {{ $cat->color }}"></i>@endif
                                {{ $cat->name }}
                            </span>
                            <span class="text-xs text-ink-400">{{ $cat->templates_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Precio --}}
            <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-4">Precio</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ([['', 'Todos'], ['free', 'Gratis'], ['paid', 'De pago']] as [$val, $lbl])
                        <a href="{{ route('templates.index', array_filter(['category'=>request('category'), 'price'=>$val ?: null, 'search'=>request('search')])) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition
                                  {{ (request('price') ?? '') === $val ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-brand-50' }}">
                            {{ $lbl }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- CTA "publica tu plantilla" (sólo decorativa por ahora) --}}
            <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-5 shadow-lift relative overflow-hidden">
                <div class="blob bg-sun-300/40 w-44 h-44 -top-10 -right-10"></div>
                <div class="relative">
                    <i class="fa-solid fa-rocket text-sun-300 text-lg"></i>
                    <h3 class="font-display font-extrabold mt-3 leading-tight">¿Tienes una plantilla?</h3>
                    <p class="text-brand-50/90 text-xs mt-2">Pronto abriremos solicitudes para vender en el marketplace.</p>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-full bg-white text-ink-900 text-xs font-bold hover:scale-[1.02] transition">
                        Avisarme <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </aside>

        {{-- ═══════════ GRID ═══════════ --}}
        <div>
            {{-- Top: contador + sort --}}
            <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                <p class="text-sm text-ink-500">
                    <strong class="text-ink-900">{{ $templates->total() }}</strong>
                    {{ $templates->total() === 1 ? 'plantilla' : 'plantillas' }}
                    @if (request('search')) que coinciden con <em>"{{ request('search') }}"</em>@endif
                </p>
                <form action="{{ route('templates.index') }}" method="GET" class="flex items-center gap-2 text-sm">
                    @foreach (['category', 'price', 'search'] as $hidden)
                        @if (request($hidden))<input type="hidden" name="{{ $hidden }}" value="{{ request($hidden) }}">@endif
                    @endforeach
                    <label for="sort" class="text-ink-500">Ordenar:</label>
                    <select id="sort" name="sort" onchange="this.form.submit()"
                            class="px-3 py-1.5 rounded-full bg-white border border-ink-200 text-sm font-semibold text-ink-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <option value=""           @selected(!request('sort'))>Destacados</option>
                        <option value="popular"    @selected(request('sort') === 'popular')>Más populares</option>
                        <option value="price_low"  @selected(request('sort') === 'price_low')>Precio: bajo a alto</option>
                        <option value="price_high" @selected(request('sort') === 'price_high')>Precio: alto a bajo</option>
                    </select>
                </form>
            </div>

            @if ($templates->isNotEmpty())
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($templates as $t)
                        <a href="{{ route('templates.show', $t->slug) }}" class="card-lift group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                            <div class="relative aspect-[16/10] bg-gradient-to-br from-coral-300 to-sun-400">
                                @if ($t->thumbnail)
                                    <img loading="lazy" decoding="async" src="{{ asset('storage/'.$t->thumbnail) }}" alt="{{ $t->title }}" class="absolute inset-0 w-full h-full object-cover">
                                @endif
                                @if ($t->category)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/95 text-[10px] font-bold uppercase tracking-wider shadow-soft"
                                          style="color: {{ $t->category->color }}">{{ $t->category->name }}</span>
                                @endif
                                @if ($t->is_free)
                                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold shadow-soft">Gratis</span>
                                @elseif ($t->has_discount)
                                    <span class="absolute top-3 right-3 px-3 py-1 rounded-full bg-coral-500 text-white text-xs font-bold shadow-soft">-{{ $t->discount_percent }}%</span>
                                @endif
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="font-display font-bold text-ink-900 leading-snug group-hover:text-brand-700 transition">{{ $t->title }}</h3>
                                @if ($t->headline)
                                    <p class="text-xs text-ink-500 mt-1.5 line-clamp-2">{{ $t->headline }}</p>
                                @endif
                                @if ($t->tech_stack)
                                    <div class="flex flex-wrap gap-1 mt-3">
                                        @foreach (array_slice($t->tech_stack, 0, 3) as $tech)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-cream-2 text-[10px] font-semibold text-ink-700 border border-ink-200">{{ $tech }}</span>
                                        @endforeach
                                        @if (count($t->tech_stack) > 3)
                                            <span class="text-[10px] text-ink-400 self-center">+{{ count($t->tech_stack) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="mt-auto pt-4 flex items-baseline justify-between">
                                    @if ($t->is_free)
                                        <span class="font-display font-extrabold text-brand-600">Gratis</span>
                                    @elseif ($t->has_discount)
                                        <span class="font-display font-extrabold text-ink-900">${{ number_format($t->discount, 0) }}<span class="text-xs text-ink-400 line-through ml-1.5">${{ number_format($t->price, 0) }}</span></span>
                                    @else
                                        <span class="font-display font-extrabold text-ink-900">${{ number_format($t->price, 0) }}</span>
                                    @endif
                                    <span class="grid place-items-center w-8 h-8 rounded-full bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition">
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">{{ $templates->onEachSide(1)->links() }}</div>
            @else
                <div class="bg-white rounded-3xl border-2 border-dashed border-ink-200 p-12 text-center">
                    <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
                        <i class="fa-solid fa-boxes-stacked text-2xl"></i>
                    </span>
                    <p class="font-display font-bold text-ink-900 mt-5">No hay plantillas con esos filtros</p>
                    <p class="text-sm text-ink-500 mt-1">Prueba quitando algún filtro.</p>
                    <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-full bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition">
                        Ver todas
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
