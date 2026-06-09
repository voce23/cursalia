@extends('layouts.admin')

@section('title', 'Nuevo enlace · Columna 2')
@section('page-title', 'Nuevo enlace · Columna 2')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.footer-column-two.index') }}" class="hover:text-brand-700">Columna 2</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">Nuevo</span>
</nav>

<form method="POST" action="{{ route('admin.footer-column-two.store') }}" class="max-w-2xl space-y-5">
    @csrf
    @include('admin.partials._footer-link-fields', ['item' => null])
    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
            <i class="fa-solid fa-floppy-disk"></i> Crear enlace
        </button>
        <a href="{{ route('admin.footer-column-two.index') }}" class="px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
    </div>
</form>

@endsection
