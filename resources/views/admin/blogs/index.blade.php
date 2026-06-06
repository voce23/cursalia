@extends('layouts.admin')

@section('title', 'Artículos del blog')
@section('page-title', 'Artículos del blog')
@section('page-subtitle', 'Crea, edita y publica las lecciones de tu curso')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-ink-700">
            <i class="fa-solid fa-newspaper text-brand-600"></i>
            {{ $items->total() }} {{ $items->total() === 1 ? 'artículo' : 'artículos' }}
        </span>
        <a href="{{ route('admin.blog-categories.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-coral-100 border border-coral-200 text-xs font-semibold text-coral-700 hover:bg-coral-200 transition">
            <i class="fa-solid fa-tags"></i> Categorías
        </a>
        <a href="{{ route('admin.blog-comments.index') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sun-100 border border-sun-300 text-xs font-semibold text-sun-700 hover:bg-sun-200 transition">
            <i class="fa-solid fa-comments"></i> Comentarios
        </a>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nuevo artículo
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('admin.blogs.index') }}" class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-4 mb-6">
    <div class="grid sm:grid-cols-[1fr_200px_200px_auto] gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1.5">Buscar</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Por título o slug…"
                class="w-full px-4 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1.5">Categoría</label>
            <select name="category" class="w-full px-3 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                <option value="">Todas</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1.5">Estado</label>
            <select name="status" class="w-full px-3 py-2.5 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                <option value="">Todos</option>
                <option value="published" @selected(request('status') === 'published')>Publicados</option>
                <option value="draft" @selected(request('status') === 'draft')>Borradores</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-2xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition text-sm">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
        </button>
    </div>
</form>

@if ($items->isNotEmpty())
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-cream-2 border-b border-ink-200/70">
                    <tr class="text-left text-[11px] font-bold uppercase tracking-wider text-ink-500">
                        <th class="py-3.5 px-5">Artículo</th>
                        <th class="py-3.5 px-5">Categoría</th>
                        <th class="py-3.5 px-5 text-center">Estado</th>
                        <th class="py-3.5 px-5">Publicado</th>
                        <th class="py-3.5 px-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($items as $b)
                        <tr class="hover:bg-cream-2/60" id="row-{{ $b->id }}">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3 max-w-md">
                                    @if ($b->thumbnail)
                                        <img src="{{ asset('storage/'.$b->thumbnail) }}" alt="" class="w-16 h-10 rounded-lg object-cover ring-1 ring-ink-200/60">
                                    @else
                                        <span class="grid place-items-center w-16 h-10 rounded-lg bg-cream-2 text-ink-400"><i class="fa-regular fa-image"></i></span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-display font-bold text-ink-900 truncate">{{ $b->title }}</p>
                                        <p class="text-xs text-ink-500 truncate font-mono">{{ $b->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-5">
                                @if ($b->category)
                                    <span class="inline-block text-xs font-bold uppercase tracking-wider" style="color: {{ $b->category->color }}">{{ $b->category->name }}</span>
                                @else
                                    <span class="text-xs text-ink-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $b->status === 'published' ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                                    {{ $b->status === 'published' ? 'Publicado' : 'Borrador' }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-ink-500 text-xs whitespace-nowrap">
                                {{ $b->published_at?->translatedFormat('d M, Y') ?: '—' }}
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($b->status === 'published')
                                        <a href="{{ route('blog.show', $b->slug) }}" target="_blank"
                                           class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Ver">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.blogs.edit', $b) }}"
                                       class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-brand-50 hover:text-brand-700 transition" title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <button type="button" onclick="deleteBlog({{ $b->id }}, '{{ addslashes($b->title) }}')"
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
        @if ($items->hasPages())
            <div class="px-5 py-4 border-t border-ink-200/70 bg-cream-2/30">{{ $items->links() }}</div>
        @endif
    </div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <span class="grid place-items-center w-16 h-16 rounded-2xl bg-cream-2 text-ink-400 mx-auto">
            <i class="fa-solid fa-newspaper text-2xl"></i>
        </span>
        <h3 class="font-display font-bold text-ink-900 mt-5">Aún no hay artículos</h3>
        <p class="text-sm text-ink-500 mt-1">Crea el primero para empezar tu blog.</p>
        <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 transition">
            <i class="fa-solid fa-plus text-xs"></i> Crear primer artículo
        </a>
    </div>
@endif

<script>
async function deleteBlog(id, name) {
    if (! confirm(`¿Eliminar "${name}"? Esta acción no se puede deshacer.`)) return;
    try {
        const res = await fetch(`/admin/blogs/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        if (res.ok) document.getElementById(`row-${id}`)?.remove();
        else alert('No se pudo eliminar');
    } catch { alert('Error de conexión'); }
}
</script>

@endsection
