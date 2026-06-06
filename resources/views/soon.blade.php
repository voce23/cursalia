@extends('layouts.app')

@section('title', $title ?? 'Próximamente')

@section('content')
<section class="relative overflow-hidden min-h-[60vh] flex items-center">
    <div class="blob bg-brand-200 w-[26rem] h-[26rem] -top-20 -left-10"></div>
    <div class="blob bg-coral-200 w-[22rem] h-[22rem] top-32 right-0"></div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-coral-500">
            <i class="fa-solid fa-hammer"></i> En construcción
        </span>
        <h1 class="font-display font-extrabold text-4xl sm:text-5xl tracking-tight mt-6 text-ink-900">
            {{ $title ?? 'Próximamente' }}
        </h1>
        <p class="text-ink-500 text-lg mt-5 max-w-xl mx-auto">
            {{ $description ?? 'Esta sección estará lista muy pronto. Mientras tanto, vuelve al inicio o crea tu cuenta.' }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver al inicio
            </a>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold border border-ink-200 hover:bg-cream-2 text-ink-700 transition">
                    Crear cuenta gratis
                </a>
            @endguest
        </div>
    </div>
</section>
@endsection
