@extends('layouts.admin')

@section('title', 'Servicios')
@section('page-title', 'Servicios y asesoría')
@section('page-subtitle', 'Planes que vendes en /services')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-ink-700">
            <i class="fa-solid fa-handshake-angle text-brand-600"></i>
            {{ $services->count() }} servicios
        </span>
        <a href="{{ route('admin.services.requests') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-coral-100 border border-coral-200 text-xs font-semibold text-coral-700 hover:bg-coral-200 transition">
            <i class="fa-regular fa-bell"></i> Bandeja de pedidos
        </a>
    </div>
    <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nuevo servicio
    </a>
</div>

@if ($services->isNotEmpty())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($services as $s)
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 flex flex-col" id="row-{{ $s->id }}">
                <div class="flex items-start gap-3">
                    <span class="grid place-items-center w-11 h-11 rounded-2xl shrink-0"
                          style="background: {{ $s->color }}1A; color: {{ $s->color }}">
                        <i class="{{ $s->icon ?: 'fa-solid fa-handshake-angle' }}"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-display font-bold text-ink-900 truncate">{{ $s->title }}</p>
                        @if ($s->headline)
                            <p class="text-xs text-ink-500 line-clamp-2 mt-0.5">{{ $s->headline }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-ink-200/70">
                    <div>
                        @if ($s->is_free)
                            <span class="font-display font-extrabold text-xl text-brand-600">Gratis</span>
                        @else
                            <span class="font-display font-extrabold text-xl text-ink-900">${{ number_format($s->price, 0) }}</span>
                        @endif
                        @if ($s->price_suffix)
                            <p class="text-[10px] text-ink-400">{{ $s->price_suffix }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if ($s->requests_count > 0)
                            <a href="{{ route('admin.services.requests') }}?service={{ $s->id }}"
                               class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-coral-100 text-coral-700 text-[10px] font-bold">
                                <i class="fa-regular fa-bell"></i> {{ $s->requests_count }}
                            </a>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                            {{ $s->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 mt-3">
                    <a href="{{ route('services.index') }}?service={{ $s->slug }}" target="_blank"
                       class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Ver">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                    <a href="{{ route('admin.services.edit', $s) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-cream-2 text-ink-700 text-sm font-semibold hover:bg-brand-50 hover:text-brand-700 transition">
                        <i class="fa-solid fa-pen text-xs"></i> Editar
                    </a>
                    <button type="button" onclick="deleteService({{ $s->id }}, '{{ addslashes($s->title) }}')"
                            class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 transition" title="Eliminar">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-handshake-angle text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay servicios</p>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
            <i class="fa-solid fa-plus text-xs"></i> Crear el primero
        </a>
    </div>
@endif

<script>
async function deleteService(id, name) {
    if (! confirm(`¿Eliminar el servicio "${name}"?`)) return;
    try {
        const res = await fetch(`/admin/services/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        if (res.ok) document.getElementById(`row-${id}`)?.remove();
        else alert('No se pudo eliminar');
    } catch { alert('Error'); }
}
</script>

@endsection
