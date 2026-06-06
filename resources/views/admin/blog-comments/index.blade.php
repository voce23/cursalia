@extends('layouts.admin')

@section('title', 'Comentarios')
@section('page-title', 'Comentarios del blog')
@section('page-subtitle', 'Aprueba o elimina los comentarios pendientes')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700">
        <i class="fa-solid fa-arrow-left text-xs"></i> Volver a artículos
    </a>
    <div class="flex flex-wrap gap-1.5">
        <a href="{{ route('admin.blog-comments.index', ['tab' => 'pending']) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition {{ $tab === 'pending' ? 'bg-coral-500 text-white' : 'bg-cream-2 text-ink-700 hover:bg-coral-50' }}">
            <i class="fa-solid fa-clock"></i> Pendientes ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.blog-comments.index', ['tab' => 'approved']) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition {{ $tab === 'approved' ? 'bg-brand-600 text-white' : 'bg-cream-2 text-ink-700 hover:bg-brand-50' }}">
            <i class="fa-solid fa-check"></i> Aprobados ({{ $counts['approved'] }})
        </a>
    </div>
</div>

@if ($items->isNotEmpty())
    <div class="space-y-3">
        @foreach ($items as $c)
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5" id="row-{{ $c->id }}">
                <div class="flex items-start gap-4">
                    <span class="grid place-items-center w-11 h-11 rounded-full bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold shrink-0">
                        {{ strtoupper(substr($c->name, 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-display font-bold text-ink-900">{{ $c->name }}</p>
                            <span class="text-xs text-ink-400">{{ $c->email }}</span>
                            <span class="text-xs text-ink-400">· {{ $c->created_at->diffForHumans() }}</span>
                            @if ($c->blog)
                                <span class="text-xs text-ink-400">· en
                                    <a href="{{ route('blog.show', $c->blog->slug) }}#comentarios" target="_blank" class="text-brand-700 hover:text-brand-600 font-semibold">{{ $c->blog->title }}</a>
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-ink-700 leading-relaxed mt-2 whitespace-pre-line bg-cream-2 rounded-2xl px-4 py-3">{{ $c->comment }}</p>
                    </div>
                    <div class="flex flex-col gap-1.5 shrink-0">
                        @if (! $c->is_approved)
                            <form method="POST" action="{{ route('admin.blog-comments.approve', $c) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 transition">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                </button>
                            </form>
                        @endif
                        <button type="button" onclick="deleteComment({{ $c->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 text-xs font-semibold transition">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $items->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-regular fa-comments text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">No hay comentarios {{ $tab === 'pending' ? 'pendientes' : 'aprobados' }}</p>
        <p class="text-sm text-ink-500 mt-1">Cuando alguien comente en el blog aparecerá aquí.</p>
    </div>
@endif

<script>
async function deleteComment(id) {
    if (! confirm('¿Eliminar este comentario?')) return;
    try {
        const res = await fetch(`/admin/blog-comments/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        if (res.ok) document.getElementById(`row-${id}`)?.remove();
    } catch { alert('Error'); }
}
</script>
@endsection
