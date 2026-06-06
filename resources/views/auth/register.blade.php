@extends('layouts.auth')

@section('title', 'Crear cuenta · Cursalia')

@section('content')
<div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center max-w-5xl mx-auto">

    {{-- Columna izquierda --}}
    <div class="hidden lg:block">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-coral-500">
            <i class="fa-solid fa-sparkles"></i> Gratis para empezar
        </span>
        <h1 class="font-display font-extrabold tracking-tight text-5xl leading-[1.05] mt-5 text-ink-900">
            Únete a la<br>academia abierta.
        </h1>
        <p class="text-ink-500 text-lg mt-5 max-w-md">
            Crea tu cuenta en segundos y empieza a tomar tus primeros cursos sin pagar nada.
        </p>

        <div class="mt-8 max-w-sm rounded-3xl bg-white border border-ink-200/70 shadow-soft p-5">
            <p class="text-sm font-semibold text-ink-900 mb-3"><i class="fa-solid fa-gift text-coral-400"></i> Al registrarte recibes:</p>
            <ul class="space-y-2.5 text-sm text-ink-700">
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-brand-600 mt-1"></i> Acceso a todos los cursos gratuitos</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-brand-600 mt-1"></i> Seguimiento de tu progreso</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-brand-600 mt-1"></i> Recordatorios y novedades por correo</li>
            </ul>
        </div>
    </div>

    {{-- Columna derecha: formulario --}}
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white border border-ink-200/70 shadow-lift rounded-3xl p-8">
            <h2 class="font-display font-extrabold text-2xl text-ink-900">Crea tu cuenta</h2>
            <p class="text-sm text-ink-500 mt-1">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-600">Inicia sesión</a></p>

            <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-5" x-data="{ role: '{{ old('role','student') }}' }">
                @csrf

                {{-- Selector de rol --}}
                <div>
                    <span class="block text-sm font-medium text-ink-700 mb-2">¿Cómo te registras?</span>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="student" x-model="role" class="sr-only peer">
                            <div class="px-3 py-3 rounded-2xl border-2 text-center transition" :class="role==='student' ? 'border-brand-500 bg-brand-50' : 'border-ink-200 bg-cream-2 hover:border-ink-300'">
                                <i class="fa-solid fa-user-graduate text-brand-600"></i>
                                <p class="text-sm font-semibold text-ink-900 mt-1">Estudiante</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="instructor" x-model="role" class="sr-only peer">
                            <div class="px-3 py-3 rounded-2xl border-2 text-center transition" :class="role==='instructor' ? 'border-coral-400 bg-coral-50' : 'border-ink-200 bg-cream-2 hover:border-ink-300'">
                                <i class="fa-solid fa-chalkboard-user text-coral-500"></i>
                                <p class="text-sm font-semibold text-ink-900 mt-1">Instructor</p>
                            </div>
                        </label>
                    </div>
                    @error('role')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="name">Nombre</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-user"></i></span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="Tu nombre">
                    </div>
                    @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Correo</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="tucorreo@ejemplo.com">
                    </div>
                    @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="password">Contraseña</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            class="w-full pl-11 pr-12 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="Mínimo 8 caracteres">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 grid place-items-center w-8 h-8 rounded-xl text-ink-400 hover:text-ink-700 hover:bg-ink-100 transition">
                            <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="password_confirmation">Confirmar contraseña</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-solid fa-lock"></i></span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                            placeholder="Repite tu contraseña">
                    </div>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    Crear mi cuenta <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <p class="text-xs text-ink-400 text-center">
                    Al continuar aceptas los términos y la política de privacidad.
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
