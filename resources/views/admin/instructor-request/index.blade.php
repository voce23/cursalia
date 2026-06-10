@extends('layouts.admin')

@section('title', 'Instructores')
@section('page-title', 'Solicitudes de instructor')
@section('page-subtitle', 'Aprueba o rechaza a quienes quieren impartir cursos')

@section('content')

@if ($instructorRequests->isNotEmpty())
    <div class="rounded-2xl bg-white border border-ink-200/70 shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-cream-2 text-ink-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-semibold">Solicitante</th>
                    <th class="px-4 py-3 font-semibold hidden sm:table-cell">Documento</th>
                    <th class="px-4 py-3 font-semibold">Estado</th>
                    <th class="px-4 py-3 font-semibold text-right">Decisión</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($instructorRequests as $req)
                    <tr class="hover:bg-cream-2/50">
                        <td class="px-4 py-3">
                            <span class="font-medium text-ink-800">{{ $req->name }}</span>
                            <span class="block text-xs text-ink-400">{{ $req->email }}</span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if ($req->document)
                                <a href="{{ route('admin.instructor-requests.download', $req) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:underline"><i class="fa-solid fa-file-arrow-down"></i> Descargar</a>
                            @else
                                <span class="text-xs text-ink-400">Sin documento</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($req->approve_status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-coral-50 text-coral-600 rounded-full px-2.5 py-1"><i class="fa-solid fa-circle-xmark"></i> Rechazado</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-sun-100 text-ink-800 rounded-full px-2.5 py-1"><i class="fa-solid fa-clock"></i> Pendiente</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.instructor-requests.update', $req) }}" onsubmit="return confirm('¿Aprobar a {{ $req->name }} como instructor?')">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-brand-600 text-white hover:bg-brand-700 transition"><i class="fa-solid fa-check"></i> Aprobar</button>
                                </form>
                                @if ($req->approve_status !== 'rejected')
                                    <form method="POST" action="{{ route('admin.instructor-requests.update', $req) }}" onsubmit="return confirm('¿Rechazar la solicitud de {{ $req->name }}?')">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-cream-2 text-coral-600 hover:bg-coral-50 transition"><i class="fa-solid fa-xmark"></i> Rechazar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $instructorRequests->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-chalkboard-user text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">No hay solicitudes pendientes</p>
        <p class="text-sm text-ink-500 mt-1">Cuando alguien pida ser instructor, aparecerá aquí para que lo apruebes.</p>
    </div>
@endif

@endsection
