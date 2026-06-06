@extends('layouts.auth')

@section('title', 'Cuenta en revisión · Cursalia')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8 text-center">
        <span class="grid place-items-center w-14 h-14 rounded-2xl bg-coral-100 text-coral-500 mx-auto">
            <i class="fa-solid fa-clock text-xl"></i>
        </span>
        <h2 class="font-display font-extrabold text-2xl text-ink-900 mt-5">Tu solicitud está en revisión</h2>
        <p class="text-sm text-ink-500 mt-3 leading-relaxed">
            Gracias por querer ser instructor en <b class="text-ink-900">Cursalia</b>. Nuestro equipo está revisando tu cuenta — recibirás un correo cuando esté aprobada.
        </p>

        <form method="POST" action="{{ route('logout') }}" class="mt-7">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold border border-ink-200 hover:bg-cream-2 text-ink-700 transition">
                Cerrar sesión <i class="fa-solid fa-right-from-bracket text-xs"></i>
            </button>
        </form>
    </div>
</div>
@endsection
