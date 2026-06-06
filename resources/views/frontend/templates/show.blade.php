@extends('layouts.app')

@section('title', $template->title)
@section('description', $template->headline ?: \Illuminate\Support\Str::limit(strip_tags($template->description), 155))

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO SPLIT (visual a la izquierda + tarjeta compra/descarga a la derecha)
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-ink-950 text-white pt-14 pb-36 overflow-hidden">
    <div class="blob bg-brand-600/30 w-[28rem] h-[28rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-500/25 w-[22rem] h-[22rem] top-20 right-0"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-white/60 mb-6">
            <a href="{{ url('/') }}" class="hover:text-white">Inicio</a>
            <i class="fa-solid fa-angle-right text-[10px]"></i>
            <a href="{{ route('templates.index') }}" class="hover:text-white">Plantillas</a>
            @if ($template->category)
                <i class="fa-solid fa-angle-right text-[10px]"></i>
                <a href="{{ route('templates.index', ['category' => $template->category->slug]) }}" class="hover:text-white">{{ $template->category->name }}</a>
            @endif
        </nav>

        <div class="grid lg:grid-cols-[1.4fr_1fr] gap-10 lg:gap-14 items-start">

            {{-- ═══════════ COLUMNA IZQUIERDA · info ═══════════ --}}
            <div>
                @if ($template->category)
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-xs font-semibold"
                          style="color: {{ $template->category->color }}">
                        @if ($template->category->icon)<i class="{{ $template->category->icon }} text-[10px]"></i>@endif
                        {{ $template->category->name }}
                    </span>
                @endif

                <h1 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl tracking-tight leading-[1.1] mt-5">{{ $template->title }}</h1>

                @if ($template->headline)
                    <p class="text-white/85 text-lg leading-relaxed mt-5 max-w-2xl">{{ $template->headline }}</p>
                @endif

                {{-- Meta info --}}
                <div class="flex flex-wrap items-center gap-x-5 gap-y-3 mt-7 text-sm">
                    <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-solid fa-code-branch text-brand-300"></i> v{{ $template->version }}</span>
                    @if ($template->downloads_count > 0)
                        <span class="inline-flex items-center gap-1.5 text-white/75"><i class="fa-solid fa-download text-brand-300"></i> {{ number_format($template->downloads_count) }} descargas</span>
                    @endif
                    @if ($template->demo_url)
                        <a href="{{ $template->demo_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sun-300 hover:text-sun-200 font-semibold">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                            Ver demo en vivo
                        </a>
                    @endif
                </div>

                {{-- Tech stack badges --}}
                @if ($template->tech_stack)
                    <div class="flex flex-wrap gap-2 mt-7">
                        @foreach ($template->tech_stack as $tech)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold text-white/85">
                                <i class="fa-solid fa-cube text-[9px] text-brand-300"></i> {{ $tech }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ═══════════ COLUMNA DERECHA · tarjeta de compra/descarga ═══════════ --}}
            <aside class="lg:sticky lg:top-24">
                <div class="bg-white text-ink-900 rounded-3xl shadow-lift border border-ink-200/70 overflow-hidden">
                    {{-- Thumbnail --}}
                    <div class="relative aspect-[16/10] bg-gradient-to-br from-brand-300 to-brand-700">
                        @if ($template->thumbnail)
                            <img loading="lazy" decoding="async" src="{{ asset('storage/'.$template->thumbnail) }}" alt="{{ $template->title }}" class="absolute inset-0 w-full h-full object-cover">
                        @endif
                        @if ($template->is_free)
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold shadow-soft">Gratis</span>
                        @elseif ($template->has_discount)
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-coral-500 text-white text-xs font-bold shadow-soft">-{{ $template->discount_percent }}%</span>
                        @endif
                    </div>

                    <div class="p-6">
                        {{-- Precio --}}
                        <div class="flex items-baseline gap-3 mb-1">
                            @if ($template->is_free)
                                <span class="font-display font-extrabold text-4xl text-brand-600">Gratis</span>
                                <span class="text-xs text-ink-500">descarga inmediata</span>
                            @elseif ($template->has_discount)
                                <span class="font-display font-extrabold text-4xl text-ink-900">${{ number_format($template->discount, 0) }}</span>
                                <span class="text-lg text-ink-400 line-through">${{ number_format($template->price, 0) }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-coral-100 text-coral-600 text-[10px] font-bold uppercase">Oferta</span>
                            @else
                                <span class="font-display font-extrabold text-4xl text-ink-900">${{ number_format($template->price, 0) }}</span>
                                <span class="text-xs text-ink-500">pago único</span>
                            @endif
                        </div>

                        {{-- Flash --}}
                        @if (session('success'))
                            <div class="mt-4 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-start gap-2">
                                <i class="fa-solid fa-circle-check mt-0.5"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        {{-- CTA principal --}}
                        @if ($template->is_free)
                            <form method="POST" action="{{ route('templates.download', $template->slug) }}" class="mt-5">
                                @csrf
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                                    <i class="fa-solid fa-download"></i> Descargar gratis
                                </button>
                            </form>
                            <p class="text-xs text-ink-400 mt-2 text-center">Sin registro · ZIP listo para deployar</p>
                        @else
                            <div x-data="{ showForm: false }" class="mt-5">
                                <button type="button" @click="showForm = true" x-show="!showForm"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                                    <i class="fa-solid fa-bell"></i> Avísame cuando esté disponible
                                </button>

                                <form x-show="showForm" x-cloak method="POST" action="{{ route('templates.waitlist', $template->slug) }}" class="space-y-3">
                                    @csrf
                                    <p class="text-xs text-ink-500">
                                        <i class="fa-solid fa-circle-info text-brand-500"></i>
                                        Esta plantilla estará a la venta en <strong>FASE 2 PRO</strong>. Déjanos tu correo y te avisamos al lanzamiento (con descuento).
                                    </p>
                                    <input type="email" name="email" required placeholder="tu@correo.com"
                                        value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white text-sm">
                                    <input type="text" name="name" placeholder="Tu nombre (opcional)"
                                        value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white text-sm">
                                    <x-math-captcha label="Confírmanos que eres humano:" />
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
                                        Unirme a la lista <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Garantías rápidas --}}
                        <ul class="mt-5 pt-5 border-t border-ink-200/70 space-y-2.5 text-xs text-ink-700">
                            @if ($template->is_free)
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Código abierto y editable</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Sin marca de agua, sin atribución</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Uso comercial permitido</li>
                            @else
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Pago único · acceso de por vida</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Actualizaciones in-app incluidas</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-brand-600"></i> Garantía de reembolso 14 días</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     LO QUE INCLUYE + DESCRIPCIÓN
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 -mt-20 relative">
    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-10 items-start">

        {{-- Descripción + features --}}
        <article class="bg-white rounded-3xl border border-ink-200/70 shadow-lift p-6 sm:p-10">
            @if ($template->description)
                <div class="prose prose-sm sm:prose-base max-w-none text-ink-700 prose-headings:font-display prose-headings:text-ink-900 prose-a:text-brand-700">
                    {!! Purifier::clean($template->description, 'richtext') !!}
                </div>
            @endif

            @if ($template->features)
                <h2 class="font-display font-extrabold text-2xl text-ink-900 mt-10 mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-brand-600 text-lg"></i> Lo que incluye
                </h2>
                <ul class="grid sm:grid-cols-2 gap-3">
                    @foreach ($template->features as $f)
                        <li class="flex items-start gap-3 p-4 rounded-2xl bg-cream-2 border border-ink-200/70">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-brand-100 text-brand-600 shrink-0 mt-0.5">
                                <i class="fa-solid fa-check text-xs"></i>
                            </span>
                            <span class="text-sm text-ink-700 font-medium">{{ $f }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        {{-- Aside: tech stack + soporte --}}
        <aside class="space-y-5">
            @if ($template->tech_stack)
                <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-4">Construida con</h3>
                    <div class="space-y-2.5">
                        @foreach ($template->tech_stack as $tech)
                            <div class="flex items-center gap-3 px-3 py-2 rounded-2xl bg-cream-2 border border-ink-200/70">
                                <span class="grid place-items-center w-9 h-9 rounded-xl bg-brand-100 text-brand-600">
                                    <i class="fa-solid fa-cube"></i>
                                </span>
                                <span class="text-sm font-semibold text-ink-900">{{ $tech }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tarjeta de servicios (¿necesitas ayuda para instalarla?) --}}
            <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-6 shadow-lift relative overflow-hidden">
                <div class="blob bg-sun-300/40 w-44 h-44 -top-10 -right-10"></div>
                <div class="relative">
                    <i class="fa-solid fa-handshake-angle text-sun-300 text-xl"></i>
                    <h3 class="font-display font-extrabold text-lg mt-3">
                        @if ($template->is_free)
                            ¿Necesitas ayuda para instalarla?
                        @else
                            ¿Quieres asesoría antes de comprar?
                        @endif
                    </h3>
                    <p class="text-sm text-brand-50/90 mt-2">
                        @if ($template->is_free)
                            La plantilla es <strong>gratis</strong>, pero si quieres ahorrarte el rato técnico te la instalamos y configuramos desde <strong>$97</strong>.
                        @else
                            Hablamos por Zoom o WhatsApp para que decidas con calma. <strong>$29 una sola sesión</strong>.
                        @endif
                    </p>
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-full bg-white text-ink-900 text-sm font-bold hover:scale-[1.02] transition">
                        Ver servicios <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                    <p class="text-[10px] text-brand-50/70 mt-3">
                        <i class="fa-solid fa-circle-info"></i> Servicios pagados (humanos) · La plantilla en sí es {{ $template->is_free ? 'gratis' : 'comprable' }}.
                    </p>
                </div>
            </div>
        </aside>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     RELACIONADAS
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($related->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-ink-200/70">
    <h2 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight text-ink-900 mb-8">También te puede interesar</h2>
    <div class="grid sm:grid-cols-3 gap-5">
        @foreach ($related as $r)
            <a href="{{ route('templates.show', $r->slug) }}" class="card-lift group bg-white rounded-3xl border border-ink-200/70 shadow-soft overflow-hidden flex flex-col">
                <div class="relative aspect-[16/10] bg-gradient-to-br from-coral-300 to-sun-400">
                    @if ($r->thumbnail)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$r->thumbnail) }}" alt="{{ $r->title }}" class="absolute inset-0 w-full h-full object-cover">
                    @endif
                    @if ($r->is_free)
                        <span class="absolute top-3 right-3 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold">Gratis</span>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    @if ($r->category)
                        <span class="text-[10px] font-bold uppercase tracking-wider" style="color: {{ $r->category->color }}">{{ $r->category->name }}</span>
                    @endif
                    <h3 class="font-display font-bold text-sm text-ink-900 mt-1 group-hover:text-brand-700 transition">{{ $r->title }}</h3>
                    <p class="mt-auto pt-3 font-display font-extrabold text-ink-900">
                        @if ($r->is_free)
                            <span class="text-brand-600">Gratis</span>
                        @else
                            ${{ number_format($r->final_price, 0) }}
                        @endif
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

@endsection
