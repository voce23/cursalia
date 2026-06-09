@extends('layouts.admin')

@section('title', 'Footer · Columna 1')
@section('page-title', 'Pie de página · Columna 1')
@section('page-subtitle', 'Enlaces de la primera columna (ej. "Explorar")')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <a href="{{ route('admin.footer.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ink-700 hover:text-brand-700">
        <i class="fa-solid fa-arrow-left"></i> Pie de página
    </a>
    <a href="{{ route('admin.footer-column-one.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
        <i class="fa-solid fa-plus text-xs"></i> Nuevo enlace
    </a>
</div>

@include('admin.partials._footer-links-table', ['items' => $items, 'editRoute' => 'admin.footer-column-one.edit', 'destroyRoute' => 'admin.footer-column-one.destroy'])

@endsection
