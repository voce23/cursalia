@extends('layouts.dashboard')

@section('title', 'Panel de instructor')
@section('page-title', 'Panel de instructor')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Saludo --}}
    <div class="bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-3xl p-7 sm:p-9 shadow-lift relative overflow-hidden">
        <div class="blob bg-sun-300/40 w-72 h-72 -top-20 -right-20"></div>
        <div class="relative">
            <p class="text-brand-100 text-sm font-medium">¡Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</p>
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl tracking-tight mt-1">Panel de instructor</h2>
            <p class="text-brand-50/90 mt-3 max-w-lg text-sm sm:text-base">
                Aquí ves el resumen de tus cursos y tus alumnos.
            </p>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        @php
            $cards = [
                ['fa-circle-check', 'Cursos aprobados', $approvedCourses, 'bg-brand-100 text-brand-600'],
                ['fa-clock', 'Cursos pendientes', $pendingCourses, 'bg-sun-100 text-sun-600'],
                ['fa-users', 'Alumnos', $totalStudents, 'bg-brand-100 text-brand-600'],
                ['fa-sack-dollar', 'Ingresos', number_format($totalEarnings, 2).' €', 'bg-coral-100 text-coral-500'],
            ];
        @endphp
        @foreach ($cards as [$icon, $label, $value, $iconClass])
            <div class="bg-white rounded-2xl border border-ink-200/70 p-5 shadow-soft">
                <span class="grid place-items-center w-11 h-11 rounded-2xl {{ $iconClass }} text-lg"><i class="fa-solid {{ $icon }}"></i></span>
                <p class="font-display font-extrabold text-2xl text-ink-900 mt-4">{{ $value }}</p>
                <p class="text-xs text-ink-500 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    {{-- Ventas recientes --}}
    <div class="bg-white rounded-3xl border border-ink-200/70 p-6 sm:p-7 shadow-soft mt-8">
        <h3 class="font-display font-bold text-lg text-ink-900">Ventas recientes</h3>
        @if ($recentSales->isNotEmpty())
            <ul class="divide-y divide-ink-100 mt-4">
                @foreach ($recentSales as $sale)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink-800 truncate">{{ $sale->course?->title ?? 'Curso' }}</p>
                            <p class="text-xs text-ink-400">{{ $sale->order?->customer?->name ?? 'Alumno' }}</p>
                        </div>
                        <span class="text-sm font-bold text-brand-600">{{ number_format((float) $sale->instructor_earning, 2) }} €</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="mt-4 rounded-2xl bg-cream-2/60 border border-dashed border-ink-200 p-8 text-center">
                <i class="fa-solid fa-chart-line text-ink-300 text-2xl"></i>
                <p class="text-sm text-ink-500 mt-2">Aún no hay ventas. Aparecerán aquí cuando tus cursos generen ingresos.</p>
            </div>
        @endif
    </div>
</div>
@endsection
