@extends('layouts.auth')

@section('title', 'Nueva contraseña · Cursalia')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8">
        <span class="grid place-items-center w-12 h-12 rounded-2xl bg-brand-100 text-brand-600">
            <i class="fa-solid fa-lock-open"></i>
        </span>
        <h2 class="font-display font-extrabold text-2xl text-ink-900 mt-5">Elige una nueva contraseña</h2>
        <p class="text-sm text-ink-500 mt-2">Asegúrate de usar una que sea segura y fácil de recordar.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Correo</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                    class="w-full px-4 py-3 rounded-2xl bg-ink-100 border border-ink-200 text-ink-500 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="password">Nueva contraseña</label>
                <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                    placeholder="Mínimo 8 caracteres">
                @error('password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                    placeholder="Repite tu contraseña">
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                Actualizar contraseña <i class="fa-solid fa-check text-xs"></i>
            </button>
        </form>
    </div>
</div>
@endsection
