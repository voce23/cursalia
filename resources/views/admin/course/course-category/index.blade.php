@extends('layouts.admin')

@section('title', 'Categorías')
@section('page-title', 'Categorías de curso')
@section('page-subtitle', 'Organiza tu catálogo por temas')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-2 text-sm">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft font-semibold text-ink-700">
            <i class="fa-solid fa-folder-tree text-brand-600"></i>
            {{ $categories->total() }} {{ $categories->total() === 1 ? 'categoría' : 'categorías' }}
        </span>
    </div>
    <a href="{{ route('admin.course-categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva categoría
    </a>
</div>

@if ($categories->isNotEmpty())
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-2 border-b border-ink-200/70">
                    <tr class="text-left text-[11px] font-bold uppercase tracking-wider text-ink-500">
                        <th class="py-3.5 px-5">Categoría</th>
                        <th class="py-3.5 px-5">Slug</th>
                        <th class="py-3.5 px-5 text-center">Subcategorías</th>
                        <th class="py-3.5 px-5">Creada</th>
                        <th class="py-3.5 px-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($categories as $cat)
                        <tr class="hover:bg-cream-2/60 transition" id="row-{{ $cat->id }}">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3">
                                    @if ($cat->image)
                                        <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" class="w-11 h-11 rounded-2xl object-cover ring-1 ring-ink-200/60">
                                    @else
                                        <span class="grid place-items-center w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-300 to-brand-500 text-white font-display font-bold text-sm">
                                            {{ strtoupper(substr($cat->name, 0, 2)) }}
                                        </span>
                                    @endif
                                    <div>
                                        <p class="font-display font-bold text-ink-900">{{ $cat->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-5">
                                <code class="px-2 py-1 rounded-lg bg-cream-2 text-ink-600 text-xs">{{ $cat->slug }}</code>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full {{ $cat->subcategories_count > 0 ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-400' }} text-xs font-bold">
                                    {{ $cat->subcategories_count }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-ink-500 text-xs whitespace-nowrap">
                                {{ $cat->created_at?->translatedFormat('d M, Y') }}
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.course-categories.edit', $cat) }}"
                                       class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <button type="button"
                                            onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
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

        @if ($categories->hasPages())
            <div class="px-5 py-4 border-t border-ink-200/70 bg-cream-2/30">
                {{ $categories->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
            <i class="fa-regular fa-folder-open text-2xl"></i>
        </span>
        <h3 class="font-display font-bold text-ink-900 mt-5">Aún no hay categorías</h3>
        <p class="text-sm text-ink-500 mt-1">Crea la primera para empezar a organizar tu catálogo.</p>
        <a href="{{ route('admin.course-categories.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
            <i class="fa-solid fa-plus text-xs"></i> Crear primera categoría
        </a>
    </div>
@endif

{{-- Confirmación de borrado --}}
<div x-data="{ open: false, id: null, name: '' }"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/60 backdrop-blur-sm"
     @open-delete.window="open = true; id = $event.detail.id; name = $event.detail.name"
     @click.self="open = false">
    <div class="bg-white rounded-3xl shadow-lift max-w-sm w-full p-7" @click.stop>
        <span class="grid place-items-center w-12 h-12 rounded-2xl bg-coral-100 text-coral-500 mx-auto">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </span>
        <h3 class="font-display font-extrabold text-xl text-ink-900 text-center mt-4">¿Eliminar categoría?</h3>
        <p class="text-sm text-ink-500 text-center mt-2">Vas a eliminar <b class="text-ink-900" x-text="name"></b>. Esta acción no se puede deshacer.</p>
        <div class="flex gap-2 mt-6">
            <button @click="open = false" class="flex-1 px-4 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</button>
            <button @click="performDelete(id); open = false" class="flex-1 px-4 py-3 rounded-2xl font-bold bg-coral-500 text-white hover:bg-coral-600 transition">Eliminar</button>
        </div>
    </div>
</div>

<script>
function deleteCategory(id, name) {
    window.dispatchEvent(new CustomEvent('open-delete', { detail: { id, name } }));
}
async function performDelete(id) {
    try {
        const res = await fetch(`/admin/course-categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (res.ok) {
            document.getElementById(`row-${id}`)?.remove();
            alert('✓ ' + (data.message || 'Eliminada'));
            location.reload();
        } else {
            alert('⚠ ' + (data.message || 'No se pudo eliminar'));
        }
    } catch (e) {
        alert('Error de conexión');
    }
}
</script>

@endsection
