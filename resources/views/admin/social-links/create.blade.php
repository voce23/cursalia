@extends('layouts.admin')

@section('title', 'Nueva red social')
@section('page-title', 'Nueva red social')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.social-links.index') }}" class="hover:text-brand-700">Redes sociales</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">Nueva</span>
</nav>

<form method="POST" action="{{ route('admin.social-links.store') }}" class="max-w-2xl space-y-5">
    @csrf
    @include('admin.partials._social-link-fields', ['item' => null])
    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
            <i class="fa-solid fa-floppy-disk"></i> Crear red social
        </button>
        <a href="{{ route('admin.social-links.index') }}" class="px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
    </div>
</form>

@endsection
