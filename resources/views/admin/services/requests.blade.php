@extends('layouts.admin')

@section('title', 'Bandeja de pedidos')
@section('page-title', 'Bandeja de pedidos de servicios')
@section('page-subtitle', 'Solicitudes recibidas en /services')

@section('content')

@php $statuses = \App\Models\ServiceRequest::STATUSES; @endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700">
        <i class="fa-solid fa-arrow-left text-xs"></i> Volver a servicios
    </a>
    {{-- Tabs de estado --}}
    <div class="flex flex-wrap gap-1.5">
        <a href="{{ route('admin.services.requests') }}"
           class="px-3 py-1.5 rounded-full text-xs font-bold transition {{ ! request('status') ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-brand-50' }}">
            Todos
        </a>
        @foreach ($statuses as $val => $lbl)
            @php $c = $counts[$val] ?? 0; @endphp
            <a href="{{ route('admin.services.requests', ['status' => $val]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition {{ request('status') === $val ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-brand-50' }}">
                {{ $lbl }} <span class="opacity-70">({{ $c }})</span>
            </a>
        @endforeach
    </div>
</div>

@if ($requests->isNotEmpty())
    <div class="space-y-3">
        @foreach ($requests as $r)
            <details class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 group">
                <summary class="cursor-pointer flex items-start gap-4 list-none">
                    <span class="grid place-items-center w-10 h-10 rounded-2xl shrink-0 mt-0.5"
                          style="background: {{ $r->service->color ?? '#10B981' }}1A; color: {{ $r->service->color ?? '#10B981' }}">
                        <i class="{{ $r->service->icon ?? 'fa-solid fa-handshake-angle' }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-display font-bold text-ink-900">{{ $r->name }}</p>
                            <a href="mailto:{{ $r->email }}" class="text-xs text-brand-700 hover:text-brand-600">{{ $r->email }}</a>
                            @if ($r->whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $r->whatsapp) }}" target="_blank" class="text-xs text-[#25D366] hover:text-[#1ea854]">
                                    <i class="fa-brands fa-whatsapp"></i> {{ $r->whatsapp }}
                                </a>
                            @endif
                        </div>
                        <p class="text-xs text-ink-500 mt-1">
                            <strong class="text-ink-700">{{ $r->service?->title ?? 'Sin servicio' }}</strong>
                            @if ($r->subject)· {{ $r->subject }}@endif
                            · {{ $r->created_at->diffForHumans() }}
                            @if ($r->budget)· Presupuesto: {{ $r->budget }}@endif
                        </p>
                    </div>
                    @php
                        $statusColors = ['new' => 'bg-coral-100 text-coral-700', 'contacted' => 'bg-sun-100 text-sun-700', 'in_progress' => 'bg-brand-100 text-brand-700', 'closed' => 'bg-ink-100 text-ink-500'];
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColors[$r->status] ?? 'bg-ink-100 text-ink-500' }}">
                        {{ $statuses[$r->status] ?? $r->status }}
                    </span>
                    <i class="fa-solid fa-chevron-down text-ink-400 group-open:rotate-180 transition"></i>
                </summary>

                <div class="mt-5 pt-5 border-t border-ink-200/70 space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-2">Mensaje</p>
                        <p class="text-sm text-ink-700 whitespace-pre-line bg-cream-2 rounded-2xl px-4 py-3">{{ $r->message }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.services.requests.update', $r) }}" class="grid sm:grid-cols-[200px_1fr_auto] gap-3 items-end">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-xs font-medium text-ink-700 mb-1.5">Estado</label>
                            <select name="status" class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                                @foreach ($statuses as $val => $lbl)
                                    <option value="{{ $val }}" @selected($r->status === $val)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-ink-700 mb-1.5">Notas internas</label>
                            <input type="text" name="admin_notes" value="{{ $r->admin_notes }}" placeholder="Notas privadas para el equipo"
                                class="w-full px-3 py-2 rounded-xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition">
                            Guardar
                        </button>
                    </form>
                </div>
            </details>
        @endforeach
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-regular fa-inbox text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">No hay pedidos con ese filtro</p>
        <p class="text-sm text-ink-500 mt-1">Cuando alguien envíe el formulario de servicios, aparecerá aquí.</p>
    </div>
@endif

@endsection
