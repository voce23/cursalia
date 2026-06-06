@extends('layouts.auth')

@section('title', 'Iniciar sesión · Cursalia')

@section('content')
<div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center max-w-5xl mx-auto">

    {{-- Columna izquierda: bienvenida --}}
    <div class="hidden lg:block">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <i class="fa-solid fa-graduation-cap"></i> Tu academia
        </span>
        <h1 class="font-display font-extrabold tracking-tight text-5xl leading-[1.05] mt-5 text-ink-900">
            ¡Qué bueno<br>verte de nuevo!
        </h1>
        <p class="text-ink-500 text-lg mt-5 max-w-md">
            Inicia sesión para continuar con tus cursos y seguir aprendiendo a tu ritmo.
        </p>

        {{-- Tres beneficios --}}
        <ul class="mt-8 space-y-4 max-w-sm">
            @foreach ([
                ['fa-circle-play','Continúa donde lo dejaste'],
                ['fa-medal','Tus certificados y progreso'],
                ['fa-bolt','Nuevos cursos cada semana'],
            ] as [$ic, $txt])
                <li class="flex items-center gap-3">
                    <span class="grid place-items-center w-9 h-9 rounded-2xl bg-brand-100 text-brand-600"><i class="fa-solid {{ $ic }}"></i></span>
                    <span class="text-ink-700">{{ $txt }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Columna derecha: formulario --}}
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8">
            <h2 class="font-display font-extrabold text-2xl text-ink-900">Inicia sesión</h2>
            <p class="text-sm text-ink-500 mt-1">¿Aún no tienes cuenta? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-600">Regístrate gratis</a></p>

            @if (session('status'))
                <div class="mt-5 px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Correo</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="tucorreo@ejemplo.com">
                    </div>
                    @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="password">Contraseña</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-12 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 grid place-items-center w-8 h-8 rounded-xl text-ink-400 hover:text-ink-700 hover:bg-ink-100 transition" aria-label="Mostrar contraseña">
                            <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 text-ink-700 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-ink-300 text-brand-600 focus:ring-brand-400">
                        Recuérdame
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-brand-700 hover:text-brand-600">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    Entrar <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-ink-400 mt-5">
            <i class="fa-solid fa-shield-halved text-brand-500"></i> Conexión segura · Cursalia nunca comparte tu información
        </p>
    </div>
</div>
@endsection
