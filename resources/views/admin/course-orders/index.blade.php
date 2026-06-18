@extends('layouts.admin')

@section('title', 'Ventas')
@section('page-title', 'Ventas de cursos')
@section('page-subtitle', 'Aprueba o rechaza los pagos por QR y transferencia')

@section('content')
@php
    $badge = [
        'pending' => ['bg-amber-100 text-amber-700', 'En revisión'],
        'approved' => ['bg-green-100 text-green-700', 'Aprobado'],
        'rejected' => ['bg-coral-100 text-coral-700', 'Rechazado'],
    ];
@endphp

@if ($orders->isEmpty())
    <div class="rounded-3xl border border-ink-200 bg-white p-10 text-center shadow-soft">
        <i class="fa-solid fa-receipt text-4xl text-ink-300"></i>
        <p class="mt-3 text-ink-500">Todavía no hay ventas registradas.</p>
        <p class="text-xs text-ink-400 mt-1">Aquí aparecerán los pagos por QR y transferencia para que los apruebes.</p>
    </div>
@else
    <div class="rounded-3xl border border-ink-200 bg-white shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-ink-400 border-b border-ink-100 bg-cream-2/40">
                        <th class="py-3 px-4">Alumno</th>
                        <th class="py-3 px-4">Curso</th>
                        <th class="py-3 px-4">Método</th>
                        <th class="py-3 px-4">Monto</th>
                        <th class="py-3 px-4">Comprobante</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($orders as $o)
                        <tr>
                            <td class="py-3 px-4">
                                <p class="font-semibold text-ink-900">{{ $o->user->name ?? '—' }}</p>
                                <p class="text-xs text-ink-400">{{ $o->user->email ?? '' }}</p>
                            </td>
                            <td class="py-3 px-4 text-ink-700">{{ $o->course->title ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="text-ink-600">{{ \App\Models\CourseOrder::METHODS[$o->method] ?? $o->method }}</span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-ink-900">{{ number_format($o->amount, 2) }} {{ $o->currency }}</td>
                            <td class="py-3 px-4">
                                @if ($o->proof_path)
                                    <a href="{{ asset('storage/'.$o->proof_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-indigo-600 font-semibold hover:underline text-xs">
                                        <i class="fa-solid fa-image"></i> Ver
                                    </a>
                                @else
                                    <span class="text-ink-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badge[$o->status][0] }}">{{ $badge[$o->status][1] }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if ($o->status === 'pending')
                                    <div class="inline-flex gap-2">
                                        <form method="POST" action="{{ route('admin.course-orders.approve', $o) }}" onsubmit="return confirm('¿Aprobar el pago y dar acceso al alumno?')">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-bold hover:bg-green-700"><i class="fa-solid fa-check"></i> Aprobar</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.course-orders.reject', $o) }}" onsubmit="return confirm('¿Rechazar este pago?')">
                                            @csrf
                                            <button class="px-3 py-1.5 rounded-lg bg-white border border-ink-200 text-ink-600 text-xs font-bold hover:bg-coral-50 hover:text-coral-600"><i class="fa-solid fa-xmark"></i> Rechazar</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-ink-400">{{ $o->updated_at->format('d/m/Y') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
@endif
@endsection
