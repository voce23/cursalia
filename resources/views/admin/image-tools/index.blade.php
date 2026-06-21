@extends('layouts.admin')

@section('title', 'Optimizar imágenes')
@section('page-title', 'Optimizar imágenes')
@section('page-subtitle', 'Convierte tus imágenes a WebP: tu academia carga más rápido y posiciona mejor')

@section('content')

@if (! $proActive)
    @include('admin.partials._pro-lock', [
        'titulo' => 'Optimizar imágenes (PRO)',
        'desc' => 'Convierte automáticamente las imágenes de tu academia a WebP para que cargue más rápido. Actívalo con tu llave Cursalia PRO.',
    ])
@else
    <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-600 text-xl"></i>
        <p class="text-sm text-green-800 font-semibold">Cursalia PRO activado.</p>
    </div>

    <div class="max-w-2xl rounded-3xl border border-ink-200 bg-white p-7 shadow-soft">
        <h3 class="font-display font-bold text-lg text-ink-900"><i class="fa-solid fa-images text-brand-500"></i> Optimizar imágenes a WebP</h3>
        <p class="text-sm text-ink-500 mt-1 leading-relaxed">
            Convierte las imágenes de <strong>cursos, categorías, blog y marcas</strong> a <strong>WebP</strong> (mucho más ligeras) y las redimensiona si son enormes.
            Tu academia cargará <strong>más rápido</strong> y mejorará el <strong>SEO</strong>. El original se reemplaza por el <code>.webp</code> y se actualizan las referencias solas.
        </p>

        <form method="POST" action="{{ route('admin.image-tools.optimize') }}" class="mt-4"
              onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Optimizando…';">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-white bg-brand-600 hover:bg-brand-700 transition">
                <i class="fa-solid fa-bolt"></i> Optimizar imágenes ahora
            </button>
        </form>

        <div class="mt-4 rounded-2xl bg-cream-2 border border-ink-200/60 px-4 py-3 text-xs text-ink-500 flex items-start gap-2">
            <i class="fa-solid fa-shield-halved text-brand-500 mt-0.5"></i>
            <span>Es seguro: solo toca imágenes subidas, conserva la transparencia (PNG) y actualiza la base de datos. Puedes ejecutarlo cuando subas imágenes nuevas.</span>
        </div>
    </div>
@endif

@endsection
