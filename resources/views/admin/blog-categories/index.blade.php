@extends('layouts.admin')

@section('title', 'Categorías de blog')
@section('page-title', 'Categorías del blog')
@section('page-subtitle', 'Organiza tus artículos por temas')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700">
        <i class="fa-solid fa-arrow-left text-xs"></i> Volver a artículos
    </a>
    <a href="{{ route('admin.blog-categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva categoría
    </a>
</div>

@if ($items->isNotEmpty())
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-cream-2 border-b border-ink-200/70">
                <tr class="text-left text-[11px] font-bold uppercase tracking-wider text-ink-500">
                    <th class="py-3.5 px-5">Categoría</th>
                    <th class="py-3.5 px-5">Slug</th>
                    <th class="py-3.5 px-5 text-center">Artículos</th>
                    <th class="py-3.5 px-5 text-center">Estado</th>
                    <th class="py-3.5 px-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-100">
                @foreach ($items as $cat)
                    <tr class="hover:bg-cream-2/60" id="row-{{ $cat->id }}">
                        <td class="py-3 px-5">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-2xl" style="background: {{ $cat->color }}"></span>
                                <p class="font-display font-bold text-ink-900">{{ $cat->name }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-5"><code class="px-2 py-1 rounded-lg bg-cream-2 text-ink-600 text-xs">{{ $cat->slug }}</code></td>
                        <td class="py-3 px-5 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full {{ $cat->blogs_count > 0 ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-400' }} text-xs font-bold">
                                {{ $cat->blogs_count }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $cat->status ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                                {{ $cat->status ? 'Activa' : 'Oculta' }}
                            </span>
                        </td>
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.blog-categories.edit', $cat) }}"
                                   class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Editar">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <button type="button" onclick="deleteCat({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                                        class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 transition" title="Eliminar">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($items->hasPages())<div class="px-5 py-4 border-t border-ink-200/70">{{ $items->links() }}</div>@endif
    </div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-tags text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">No hay categorías</p>
        <a href="{{ route('admin.blog-categories.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
            Crear la primera
        </a>
    </div>
@endif

<script>
async function deleteCat(id, name) {
    if (! confirm(`¿Eliminar la categoría "${name}"?`)) return;
    try {
        const res = await fetch(`/admin/blog-categories/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) document.getElementById(`row-${id}`)?.remove();
        else alert(data.message || 'Error');
    } catch { alert('Error de conexión'); }
}
</script>
@endsection
