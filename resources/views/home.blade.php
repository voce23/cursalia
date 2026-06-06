@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<section class="relative overflow-hidden">
    {{-- Blobs decorativos --}}
    <div class="blob bg-brand-200 w-[28rem] h-[28rem] -top-24 -left-16"></div>
    <div class="blob bg-coral-200 w-[24rem] h-[24rem] top-32 -right-10"></div>
    <div class="blob bg-sun-200 w-[22rem] h-[22rem] bottom-0 left-1/3"></div>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-32">

        {{-- Etiqueta superior --}}
        <div class="sr text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-ink-700">
                <span class="relative flex w-2 h-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                </span>
                Sprint 0 listo · Cimientos en su sitio
            </span>
        </div>

        {{-- Titular --}}
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-center text-5xl sm:text-6xl lg:text-7xl leading-[1.05] mt-7 text-ink-900">
            Tu academia,<br class="hidden sm:inline">
            <span class="text-brand-600">a tu manera</span>.
        </h1>

        {{-- Subtítulo --}}
        <p class="sr s2 text-lg text-ink-500 leading-relaxed mt-6 max-w-2xl mx-auto text-center">
            Bienvenido a <b class="text-ink-900">Cursalia</b>. Si ves este aire, estos colores y esta tipografía,
            los cimientos del proyecto ya están funcionando.
        </p>

        {{-- Mini-stack --}}
        <div class="sr s3 flex flex-wrap items-center justify-center gap-2 mt-8">
            @foreach (['Laravel 13', 'Tailwind 4', 'Alpine.js', 'Poppins + Inter'] as $tech)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/70 border border-ink-200/70 text-xs font-medium text-ink-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                    {{ $tech }}
                </span>
            @endforeach
        </div>

        {{-- Cards de verificación con paleta variada --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-14">
            @php
                $checks = [
                    ['Base de datos',  'cursalia · MySQL',     'brand', 'database'],
                    ['Tailwind 4',     'tokens cargados',      'coral', 'palette'],
                    ['Alpine.js',      'reactividad lista',    'sun',   'bolt'],
                    ['Tipografía',     'Poppins + Inter',      'brand', 'font'],
                ];
            @endphp
            @foreach ($checks as $i => [$title, $sub, $color, $icon])
                <div class="sr s{{ ($i % 4) + 1 }} card-lift bg-white rounded-3xl border border-ink-200/70 shadow-soft p-5">
                    <span class="grid place-items-center w-11 h-11 rounded-2xl
                        @if($color === 'brand') bg-brand-100 text-brand-600
                        @elseif($color === 'coral') bg-coral-100 text-coral-500
                        @else bg-sun-100 text-sun-500 @endif">
                        @switch($icon)
                            @case('database')<i class="fa-solid fa-database"></i>@break
                            @case('palette')<i class="fa-solid fa-palette"></i>@break
                            @case('bolt')<i class="fa-solid fa-bolt"></i>@break
                            @case('font')<i class="fa-solid fa-font"></i>@break
                        @endswitch
                    </span>
                    <h3 class="font-display font-bold text-ink-900 mt-4">{{ $title }}</h3>
                    <p class="text-sm text-ink-500 mt-1">{{ $sub }}</p>
                    <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-brand-600">
                        <i class="fa-solid fa-circle-check"></i> Listo
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paleta de colores --}}
        <div class="sr mt-16 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-ink-400 mb-5">Paleta Cursalia</p>
            <div class="flex flex-wrap items-center justify-center gap-2.5">
                @foreach ([
                    ['brand-500', 'Esmeralda', '#10B981'],
                    ['coral-400', 'Coral',     '#FB7185'],
                    ['sun-400',   'Sol',       '#FBBF24'],
                    ['ink-900',   'Carbón',    '#1F2933'],
                    ['cream',     'Crema',     '#FBFAF7'],
                ] as [$cls, $name, $hex])
                    <div class="group inline-flex items-center gap-2 pl-1 pr-3 py-1 rounded-full bg-white border border-ink-200/70 shadow-soft hover:shadow-lift transition">
                        <span class="w-6 h-6 rounded-full bg-{{ $cls }} {{ $cls === 'cream' ? 'border border-ink-200' : '' }}"></span>
                        <span class="text-xs font-medium text-ink-700">{{ $name }}</span>
                        <span class="text-[10px] font-mono text-ink-400 group-hover:text-ink-700 transition">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Próximo paso --}}
        <div class="sr mt-16 max-w-2xl mx-auto rounded-3xl bg-white border border-ink-200/70 shadow-soft p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <span class="grid place-items-center w-12 h-12 shrink-0 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft">
                <i class="fa-solid fa-rocket"></i>
            </span>
            <div class="flex-1">
                <h3 class="font-display font-bold text-ink-900">Próximo paso · Sprint 1</h3>
                <p class="text-sm text-ink-500 mt-1">Integrar el diseño del home de Claude.ai y conectar los datos reales (cursos, categorías, instructores, testimonios).</p>
            </div>
        </div>

    </div>
</section>
@endsection
