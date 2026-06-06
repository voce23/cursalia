@extends('layouts.auth')

@section('title', 'Recuperar contraseña · Cursalia')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8">
        <span class="grid place-items-center w-12 h-12 rounded-2xl bg-coral-100 text-coral-500">
            <i class="fa-solid fa-key"></i>
        </span>
        <h2 class="font-display font-extrabold text-2xl text-ink-900 mt-5">¿Olvidaste tu contraseña?</h2>
        <p class="text-sm text-ink-500 mt-2">No te preocupes. Escribe tu correo y te enviaremos un enlace para crear una nueva.</p>

        @if (session('status'))
            <div class="mt-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-start gap-2">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Correo</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-envelope"></i></span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                        placeholder="tucorreo@ejemplo.com">
                </div>
                @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                Enviar enlace <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </form>

        <p class="text-center text-sm text-ink-500 mt-6">
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-600"><i class="fa-solid fa-arrow-left text-xs"></i> Volver a iniciar sesión</a>
        </p>
    </div>
</div>
@endsection
