@extends('layouts.admin')

@section('title', 'Tarjetas de contacto')
@section('page-title', 'Tarjetas de contacto')
@section('page-subtitle', 'Los recuadros (email, teléfono, dirección…) de la página /contact')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.contact-settings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700">
        <i class="fa-solid fa-arrow-left"></i> Página de contacto
    </a>
    <a href="{{ route('admin.contact-cards.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva tarjeta
    </a>
</div>

@if ($items->isNotEmpty())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($items as $item)
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
                <div class="flex items-start gap-3">
                    <span class="grid place-items-center w-11 h-11 rounded-2xl bg-brand-100 text-brand-600 shrink-0">
                        <i class="{{ $item->icon ?: 'fa-solid fa-circle-info' }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-display font-bold text-ink-900 truncate">{{ $item->title }}</p>
                        @if ($item->line_one)<p class="text-xs text-ink-600 truncate mt-0.5">{{ $item->line_one }}</p>@endif
                        @if ($item->line_two)<p class="text-xs text-ink-400 truncate">{{ $item->line_two }}</p>@endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-ink-200/70">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $item->is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                        {{ $item->is_active ? 'Visible' : 'Oculto' }} · orden {{ $item->sort_order }}
                    </span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.contact-cards.edit', $item) }}" class="grid place-items-center w-8 h-8 rounded-lg text-ink-500 hover:bg-ink-100" title="Editar"><i class="fa-solid fa-pen text-xs"></i></a>
                        <form method="POST" action="{{ route('admin.contact-cards.destroy', $item) }}" onsubmit="return confirm('¿Eliminar esta tarjeta?')">@csrf @method('DELETE')<button class="grid place-items-center w-8 h-8 rounded-lg text-coral-500 hover:bg-coral-50" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $items->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-address-card text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay tarjetas</p>
        <p class="text-sm text-ink-500 mt-1">Añade tu email, teléfono o dirección con el botón de arriba.</p>
    </div>
@endif

@endsection
