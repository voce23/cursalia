@extends('layouts.admin')

@section('title', 'Comentarios de lecciones')
@section('page-title', 'Comentarios de lecciones')
@section('page-subtitle', 'Modera los comentarios que dejan los alumnos en las lecciones')

@section('content')

@if (session('success'))
    <div class="mb-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-start gap-2">
        <i class="fa-solid fa-circle-check mt-0.5"></i><span>{{ session('success') }}</span>
    </div>
@endif

{{-- Pestañas --}}
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('admin.lesson-comments.index', ['tab' => 'pending']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition {{ $tab === 'pending' ? 'bg-coral-100 border-coral-200 text-coral-700' : 'bg-white border-ink-200 text-ink-600 hover:bg-cream-2' }}">
        <i class="fa-solid fa-clock"></i> Pendientes
        <span class="px-1.5 rounded-full bg-coral-500 text-white text-[11px]">{{ $counts['pending'] }}</span>
    </a>
    <a href="{{ route('admin.lesson-comments.index', ['tab' => 'approved']) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold border transition {{ $tab === 'approved' ? 'bg-brand-100 border-brand-200 text-brand-700' : 'bg-white border-ink-200 text-ink-600 hover:bg-cream-2' }}">
        <i class="fa-solid fa-circle-check"></i> Aprobados
        <span class="px-1.5 rounded-full bg-brand-500 text-white text-[11px]">{{ $counts['approved'] }}</span>
    </a>
</div>

@if ($items->isNotEmpty())
    <div class="space-y-3">
        @foreach ($items as $item)
            <div class="bg-white border border-ink-200/70 rounded-2xl shadow-soft p-5">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap text-sm">
                            <span class="font-bold text-ink-900">{{ $item->name }}</span>
                            <span class="text-ink-400">·</span>
                            <span class="text-ink-500">{{ $item->email }}</span>
                            <span class="text-ink-400">·</span>
                            <span class="text-xs text-ink-400">{{ $item->created_at?->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-ink-400 mt-0.5">
                            <i class="fa-solid fa-book-open mr-1"></i> {{ $item->lesson?->title ?? 'Lección eliminada' }}
                        </p>
                        <p class="text-sm text-ink-700 leading-relaxed mt-2">{{ $item->comment }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @unless ($item->is_approved)
                            <form method="POST" action="{{ route('admin.lesson-comments.approve', $item->id) }}">
                                @csrf
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-brand-600 text-white hover:bg-brand-700 transition">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                </button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('admin.lesson-comments.destroy', $item->id) }}" onsubmit="return confirm('¿Eliminar este comentario?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-coral-50 text-coral-600 border border-coral-200 hover:bg-coral-100 transition">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $items->links() }}</div>
@else
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-10 text-center text-ink-500">
        <i class="fa-regular fa-comments text-3xl text-ink-300 mb-3"></i>
        <p>No hay comentarios {{ $tab === 'pending' ? 'pendientes' : 'aprobados' }}.</p>
    </div>
@endif

@endsection
