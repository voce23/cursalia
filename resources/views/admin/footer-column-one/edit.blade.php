@extends('layouts.admin')

@section('title', 'Editar enlace · Columna 1')
@section('page-title', 'Editar enlace · Columna 1')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.footer-column-one.index') }}" class="hover:text-brand-700">Columna 1</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">{{ $item->title }}</span>
</nav>

<form method="POST" action="{{ route('admin.footer-column-one.update', $item) }}" class="max-w-2xl space-y-5">
    @csrf @method('PUT')
    @include('admin.partials._footer-link-fields', ['item' => $item])
    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
        <a href="{{ route('admin.footer-column-one.index') }}" class="px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
    </div>
</form>

@endsection
