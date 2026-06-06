@extends('layouts.auth')

@section('title', 'Verifica tu correo · Cursalia')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8 text-center">
        <span class="grid place-items-center w-14 h-14 rounded-2xl bg-sun-100 text-sun-500 mx-auto">
            <i class="fa-solid fa-envelope-circle-check text-xl"></i>
        </span>
        <h2 class="font-display font-extrabold text-2xl text-ink-900 mt-5">Verifica tu correo</h2>
        <p class="text-sm text-ink-500 mt-3 leading-relaxed">
            Te enviamos un enlace de verificación. Haz click en el enlace para activar tu cuenta y continuar.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="mt-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm">
                <i class="fa-solid fa-circle-check"></i> Te reenviamos el enlace. Revisa tu bandeja.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                Reenviar enlace <i class="fa-solid fa-rotate-right text-xs"></i>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="text-sm text-ink-500 hover:text-ink-900">Cerrar sesión</button>
        </form>
    </div>
</div>
@endsection
