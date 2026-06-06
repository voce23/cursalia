@extends('layouts.admin')

@section('title', 'Plantillas')
@section('page-title', 'Marketplace de plantillas')
@section('page-subtitle', 'Productos digitales que vendes en /templates')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-ink-700">
            <i class="fa-solid fa-boxes-stacked text-brand-600"></i>
            {{ $templates->total() }} {{ $templates->total() === 1 ? 'plantilla' : 'plantillas' }}
        </span>
        <a href="{{ route('admin.templates.waitlist') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-coral-100 border border-coral-200 text-xs font-semibold text-coral-700 hover:bg-coral-200 transition">
            <i class="fa-solid fa-bell"></i> Lista de espera
        </a>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva plantilla
    </a>
</div>

@if ($templates->isNotEmpty())
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-2 border-b border-ink-200/70">
                    <tr class="text-left text-[11px] font-bold uppercase tracking-wider text-ink-500">
                        <th class="py-3.5 px-5">Plantilla</th>
                        <th class="py-3.5 px-5">Categoría</th>
                        <th class="py-3.5 px-5 text-right">Precio</th>
                        <th class="py-3.5 px-5 text-center">Estado</th>
                        <th class="py-3.5 px-5 text-center">Espera</th>
                        <th class="py-3.5 px-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($templates as $t)
                        <tr class="hover:bg-cream-2/60" id="row-{{ $t->id }}">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3">
                                    @if ($t->thumbnail)
                                        <img src="{{ asset('storage/'.$t->thumbnail) }}" alt="" class="w-14 h-10 rounded-lg object-cover ring-1 ring-ink-200/60">
                                    @else
                                        <span class="grid place-items-center w-14 h-10 rounded-lg bg-cream-2 text-ink-400"><i class="fa-solid fa-image"></i></span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-display font-bold text-ink-900 truncate">{{ $t->title }}</p>
                                        <p class="text-xs text-ink-500 truncate">{{ $t->headline }}</p>
                                    </div>
                                    @if ($t->is_featured)
                                        <i class="fa-solid fa-star text-sun-500 text-xs" title="Destacada"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5">
                                @if ($t->category)
                                    <span class="inline-block text-xs font-bold uppercase tracking-wider" style="color: {{ $t->category->color }}">{{ $t->category->name }}</span>
                                @else
                                    <span class="text-xs text-ink-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                @if ($t->is_free)
                                    <span class="font-bold text-brand-600">Gratis</span>
                                @elseif ($t->has_discount)
                                    <span class="font-bold text-ink-900">${{ number_format($t->discount, 0) }}</span>
                                    <span class="text-xs text-ink-400 line-through">${{ number_format($t->price, 0) }}</span>
                                @else
                                    <span class="font-bold text-ink-900">${{ number_format($t->price, 0) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $t->status === 'published' ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $t->status === 'published' ? 'Publicada' : 'Borrador' }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full {{ $t->waitlist_count > 0 ? 'bg-coral-100 text-coral-700' : 'bg-ink-100 text-ink-400' }} text-xs font-bold">
                                    {{ $t->waitlist_count }}
                                </span>
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('templates.show', $t->slug) }}" target="_blank"
                                       class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Ver">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.edit', $t) }}"
                                       class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <button type="button" onclick="deleteTemplate({{ $t->id }}, '{{ addslashes($t->title) }}')"
                                            class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 transition" title="Eliminar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($templates->hasPages())
            <div class="px-5 py-4 border-t border-ink-200/70 bg-cream-2/30">{{ $templates->links() }}</div>
        @endif
    </div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
            <i class="fa-solid fa-boxes-stacked text-2xl"></i>
        </span>
        <h3 class="font-display font-bold text-ink-900 mt-5">Aún no hay plantillas</h3>
        <a href="{{ route('admin.templates.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
            <i class="fa-solid fa-plus text-xs"></i> Crear primera
        </a>
    </div>
@endif

<script>
async function deleteTemplate(id, name) {
    if (! confirm(`¿Eliminar la plantilla "${name}"?`)) return;
    try {
        const res = await fetch(`/admin/templates/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (res.ok) { document.getElementById(`row-${id}`)?.remove(); }
        else { alert('⚠ ' + (data.message || 'No se pudo eliminar')); }
    } catch (e) { alert('Error de conexión'); }
}
</script>

@endsection
