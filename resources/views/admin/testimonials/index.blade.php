@extends('layouts.admin')

@section('title', 'Testimonios')
@section('page-title', 'Testimonios')
@section('page-subtitle', 'Reseñas que aparecen en el inicio y en la página "Nosotros"')

@section('content')

<div class="flex items-center justify-end mb-6">
    <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nuevo testimonio
    </a>
</div>

@if ($items->isNotEmpty())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($items as $item)
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5 flex flex-col">
                <p class="text-sun-500 text-sm">{{ str_repeat('★', max(1, min(5, (int) $item->rating))) }}<span class="text-ink-200">{{ str_repeat('★', 5 - max(1, min(5, (int) $item->rating))) }}</span></p>
                <blockquote class="text-sm text-ink-700 mt-2 line-clamp-4 flex-1">"{{ $item->message }}"</blockquote>
                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-ink-200/70">
                    @if ($item->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->avatar) }}" alt="" class="w-9 h-9 rounded-full object-cover">
                    @else
                        <span class="grid place-items-center w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-sm">{{ strtoupper(substr($item->name, 0, 1)) }}</span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-ink-900 truncate">{{ $item->name }}</p>
                        @if ($item->designation)<p class="text-xs text-ink-400 truncate">{{ $item->designation }}</p>@endif
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                        {{ $item->is_active ? 'Visible' : 'Oculto' }}
                    </span>
                </div>
                <div class="flex items-center gap-1.5 mt-3">
                    <a href="{{ route('admin.testimonials.edit', $item) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-cream-2 text-ink-700 text-sm font-semibold hover:bg-brand-50 hover:text-brand-700 transition"><i class="fa-solid fa-pen text-xs"></i> Editar</a>
                    <form method="POST" action="{{ route('admin.testimonials.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este testimonio?')">@csrf @method('DELETE')<button class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 transition" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $items->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-quote-left text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay testimonios</p>
        <p class="text-sm text-ink-500 mt-1">La sección de reseñas se oculta sola hasta que crees el primero.</p>
    </div>
@endif

@endsection
