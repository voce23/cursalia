@extends('layouts.admin')

@section('title', 'Resumen')
@section('page-title', 'Resumen del panel')
@section('page-subtitle', 'Cómo va Cursalia esta semana')

@section('content')

{{-- ════════════════════ KPIs Principales ════════════════════ --}}
@php
    $kpis = [
        ['Estudiantes',    $totalStudents,    'fa-user-graduate',    'brand'],
        ['Instructores',   $totalInstructors, 'fa-chalkboard-user',  'coral'],
        ['Cursos activos', $approvedCourses,  'fa-book-open',        'sun'],
        ['Por aprobar',    $pendingCourses,   'fa-hourglass-half',   'brand'],
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($kpis as [$lbl, $val, $ic, $color])
        <div class="card-lift bg-white border border-ink-200/70 rounded-3xl p-5 shadow-soft">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs text-ink-500 font-medium">{{ $lbl }}</p>
                    <p class="font-display font-extrabold text-3xl text-ink-900 mt-1">{{ number_format($val) }}</p>
                </div>
                <span class="grid place-items-center w-11 h-11 rounded-2xl
                    @if($color === 'brand') bg-brand-100 text-brand-600
                    @elseif($color === 'coral') bg-coral-100 text-coral-500
                    @else bg-sun-100 text-sun-500 @endif">
                    <i class="fa-solid {{ $ic }}"></i>
                </span>
            </div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6 mt-6">

    {{-- ════════════════════ Estado de FASE 1 ════════════════════ --}}
    <div class="lg:col-span-2 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-6 sm:p-8 shadow-lift relative overflow-hidden">
        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> FASE 1 — FREE en marcha
            </span>
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl mt-4 leading-tight">
                Cursalia se está poblando, jefe 👋
            </h2>
            <p class="text-brand-50/90 mt-3 max-w-lg text-sm sm:text-base">
                Tienes {{ $totalStudents }} estudiantes, {{ $approvedCourses }} cursos activos y {{ $totalInstructors }} instructores.
                Los pagos y el marketplace llegan en <b>FASE 2 (PRO)</b>.
            </p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ route('admin.course-categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white text-ink-900 font-bold text-sm hover:scale-[1.02] transition">
                    <i class="fa-solid fa-folder-tree"></i> Gestionar categorías
                </a>
                <a href="{{ url('/') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 border border-white/20 font-semibold text-sm hover:bg-white/15 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Ver sitio público
                </a>
            </div>
        </div>
    </div>

    {{-- Próximas funciones (FASE 2) --}}
    <div class="rounded-3xl bg-ink-950 text-white p-6 shadow-lift relative overflow-hidden">
        <div class="blob bg-coral-500/30 w-44 h-44 -top-10 -right-10"></div>
        <div class="relative">
            <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-coral-500/20 text-coral-300 text-[10px] font-bold uppercase tracking-wider">
                FASE 2 · PRO
            </span>
            <h3 class="font-display font-extrabold text-xl mt-4 leading-tight">Lo que viene</h3>
            <ul class="mt-4 space-y-2.5 text-sm text-white/75">
                @foreach ([
                    ['fa-credit-card', 'Pagos Stripe/PayPal'],
                    ['fa-cart-shopping', 'Carrito y cupones'],
                    ['fa-shop', 'Marketplace multi-instructor'],
                    ['fa-medal', 'Certificados al completar'],
                ] as [$ic, $txt])
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid {{ $ic }} text-coral-300 w-4"></i> {{ $txt }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mt-6">

    {{-- ════════════════════ Cursos recientes ════════════════════ --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
        <div class="flex items-center justify-between gap-3 mb-5">
            <h3 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-book-open text-brand-600 text-base"></i> Cursos recientes
            </h3>
            <span class="text-xs text-ink-400">últimos 5</span>
        </div>
        @if ($recentCourses->isNotEmpty())
            <ul class="divide-y divide-ink-100">
                @foreach ($recentCourses as $c)
                    <li class="py-3 flex items-center gap-3">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-brand-300 to-brand-500 text-white font-display font-bold text-xs shrink-0">
                            {{ strtoupper(substr($c->title, 0, 2)) }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-ink-900 truncate">{{ $c->title }}</p>
                            <p class="text-xs text-ink-500 truncate">{{ $c->instructor?->name ?? 'Sin instructor' }} · {{ $c->created_at?->diffForHumans() }}</p>
                        </div>
                        @php
                            $bg = match ($c->is_approved) {
                                'approved' => 'bg-brand-100 text-brand-700',
                                'pending'  => 'bg-sun-100 text-sun-600',
                                'rejected' => 'bg-coral-100 text-coral-600',
                                default    => 'bg-ink-100 text-ink-500',
                            };
                            $lbl = match ($c->is_approved) {
                                'approved' => 'Aprobado',
                                'pending'  => 'Pendiente',
                                'rejected' => 'Rechazado',
                                default    => '—',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $bg }}">{{ $lbl }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-ink-500 text-center py-8">No hay cursos recientes.</p>
        @endif
    </div>

    {{-- ════════════════════ Blogs recientes ════════════════════ --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
        <div class="flex items-center justify-between gap-3 mb-5">
            <h3 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                <i class="fa-solid fa-newspaper text-coral-500 text-base"></i> Publicaciones del blog
            </h3>
            <span class="text-xs text-ink-400">últimas 5</span>
        </div>
        @if ($recentBlogs->isNotEmpty())
            <ul class="divide-y divide-ink-100">
                @foreach ($recentBlogs as $b)
                    <li class="py-3 flex items-center gap-3">
                        <span class="grid place-items-center w-10 h-10 rounded-2xl bg-gradient-to-br from-coral-300 to-sun-400 text-white shrink-0">
                            <i class="fa-solid fa-pen-nib text-sm"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-ink-900 truncate">{{ $b->title }}</p>
                            <p class="text-xs text-ink-500 truncate">{{ $b->author?->name ?? '—' }} · {{ $b->created_at?->diffForHumans() }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $b->status === 'published' ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                            {{ $b->status === 'published' ? 'Publicado' : 'Borrador' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-ink-500 text-center py-8">Aún no hay artículos.</p>
        @endif
    </div>
</div>

{{-- ════════════════════ Ingresos (BLOQUEADOS FASE 2) ════════════════════ --}}
<div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 mt-6 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-white/60 to-white/85 backdrop-blur-[1px] rounded-3xl grid place-items-center z-10">
        <div class="text-center max-w-md px-6">
            <span class="grid place-items-center w-14 h-14 rounded-2xl bg-ink-950 text-white mx-auto">
                <i class="fa-solid fa-lock"></i>
            </span>
            <h3 class="font-display font-extrabold text-xl text-ink-900 mt-4">Métricas de ingresos · FASE 2</h3>
            <p class="text-sm text-ink-500 mt-2">Los gráficos de ventas, órdenes y comisiones se activan cuando integremos Stripe y PayPal.</p>
            <span class="inline-flex items-center gap-2 mt-4 px-3 py-1 rounded-full bg-ink-100 text-ink-700 text-xs font-semibold">
                <i class="fa-solid fa-clock text-coral-500 text-[10px]"></i> Próximamente
            </span>
        </div>
    </div>

    <div class="opacity-30">
        <h3 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-brand-600 text-base"></i> Ingresos
        </h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
            @foreach ([
                ['Hoy',       '$0'],
                ['Semana',    '$0'],
                ['Mes',       '$0'],
                ['Año',       '$0'],
            ] as [$lbl, $val])
                <div class="bg-cream-2 border border-ink-200/70 rounded-2xl p-4">
                    <p class="text-xs text-ink-500">{{ $lbl }}</p>
                    <p class="font-display font-extrabold text-2xl text-ink-900 mt-1">{{ $val }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
