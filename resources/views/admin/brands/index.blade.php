@extends('layouts.admin')

@section('title', 'Marcas')
@section('page-title', 'Marcas / logos')
@section('page-subtitle', 'Logos de empresas o aliados que se muestran en el inicio')

@section('content')

<div class="flex items-center justify-end mb-6">
    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nueva marca
    </a>
</div>

@if ($brands->isNotEmpty())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($brands as $brand)
            <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-5">
                <div class="flex items-center gap-4">
                    <div class="grid place-items-center w-20 h-14 rounded-xl bg-cream-2 shrink-0">
                        @if ($brand->logo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($brand->logo) }}" alt="{{ $brand->name }}" class="max-h-10 max-w-[64px] object-contain">
                        @else
                            <i class="fa-regular fa-image text-ink-300 text-xl"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-display font-bold text-ink-900 truncate">{{ $brand->name }}</p>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold mt-1 {{ $brand->is_active ? 'bg-brand-100 text-brand-700' : 'bg-ink-100 text-ink-500' }}">
                            {{ $brand->is_active ? 'Visible' : 'Oculto' }} · orden {{ $brand->sort_order }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 mt-4 pt-4 border-t border-ink-200/70">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-cream-2 text-ink-700 text-sm font-semibold hover:bg-brand-50 hover:text-brand-700 transition"><i class="fa-solid fa-pen text-xs"></i> Editar</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('¿Eliminar esta marca?')">@csrf @method('DELETE')<button class="grid place-items-center w-9 h-9 rounded-xl bg-cream-2 text-ink-700 hover:bg-coral-50 hover:text-coral-600 transition" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button></form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $brands->links() }}</div>
@else
    <div class="bg-white border-2 border-dashed border-ink-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-building text-3xl text-ink-300"></i>
        <p class="font-display font-bold text-ink-900 mt-4">Aún no hay marcas</p>
        <p class="text-sm text-ink-500 mt-1">La franja de logos se oculta sola hasta que añadas la primera.</p>
    </div>
@endif

@endsection
