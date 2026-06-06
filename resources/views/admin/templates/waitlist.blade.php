@extends('layouts.admin')

@section('title', 'Lista de espera')
@section('page-title', 'Lista de espera')
@section('page-subtitle', 'Personas interesadas en plantillas de pago (FASE 2)')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700 transition">
        <i class="fa-solid fa-arrow-left text-xs"></i> Volver a plantillas
    </a>
    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-coral-100 border border-coral-200 text-xs font-semibold text-coral-700">
        <i class="fa-solid fa-bell"></i> {{ $entries->total() }} {{ $entries->total() === 1 ? 'persona en espera' : 'personas en espera' }}
    </span>
</div>

<div class="grid lg:grid-cols-[1fr_280px] gap-6 items-start">

    {{-- Tabla de entradas --}}
    @if ($entries->isNotEmpty())
        <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-cream-2 border-b border-ink-200/70">
                        <tr class="text-left text-[11px] font-bold uppercase tracking-wider text-ink-500">
                            <th class="py-3.5 px-5">Email</th>
                            <th class="py-3.5 px-5">Nombre</th>
                            <th class="py-3.5 px-5">Plantilla</th>
                            <th class="py-3.5 px-5 text-right">Cuándo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @foreach ($entries as $e)
                            <tr class="hover:bg-cream-2/60">
                                <td class="py-3 px-5">
                                    <a href="mailto:{{ $e->email }}" class="text-brand-700 hover:text-brand-600 break-all">{{ $e->email }}</a>
                                </td>
                                <td class="py-3 px-5 text-ink-700">{{ $e->name ?: '—' }}</td>
                                <td class="py-3 px-5">
                                    @if ($e->template)
                                        <a href="{{ route('templates.show', $e->template->slug) }}" target="_blank" class="text-ink-700 hover:text-brand-700">
                                            {{ $e->template->title }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    @else
                                        <span class="text-ink-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-5 text-right text-ink-500 text-xs whitespace-nowrap">{{ $e->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($entries->hasPages())
                <div class="px-5 py-4 border-t border-ink-200/70">{{ $entries->links() }}</div>
            @endif
        </div>
    @else
        <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
            <i class="fa-solid fa-inbox text-3xl text-ink-300"></i>
            <p class="font-display font-bold text-ink-900 mt-4">Aún no hay registros</p>
            <p class="text-sm text-ink-500 mt-1">Cuando alguien se suscriba a una plantilla, aparecerá aquí.</p>
        </div>
    @endif

    {{-- Ranking por plantilla --}}
    <aside class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
        <h3 class="text-xs font-bold uppercase tracking-wider text-ink-400 mb-4">Ranking por plantilla</h3>
        <div class="space-y-2">
            @foreach ($countByTemplate as $c)
                <div class="flex items-center justify-between px-3 py-2 rounded-2xl {{ $c->waitlist_count > 0 ? 'bg-coral-50' : 'bg-cream-2' }} text-sm">
                    <a href="{{ route('templates.show', $c->slug) }}" target="_blank" class="truncate hover:text-brand-700">{{ $c->title }}</a>
                    <span class="font-bold {{ $c->waitlist_count > 0 ? 'text-coral-700' : 'text-ink-400' }}">{{ $c->waitlist_count }}</span>
                </div>
            @endforeach
        </div>
    </aside>
</div>

@endsection
