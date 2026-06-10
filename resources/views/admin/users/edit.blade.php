@extends('layouts.admin')

@section('title', 'Editar usuario')
@section('page-title', 'Editar usuario')

@section('content')

<nav class="flex items-center gap-2 text-sm text-ink-500 mb-5">
    <a href="{{ route('admin.users.index') }}" class="hover:text-brand-700">Usuarios</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <a href="{{ route('admin.users.show', $user) }}" class="hover:text-brand-700">{{ $user->name }}</a>
    <i class="fa-solid fa-angle-right text-[10px] text-ink-300"></i>
    <span class="text-ink-900 font-medium">Editar</span>
</nav>

<form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-xl space-y-5">
    @csrf @method('PUT')

    {{-- Datos --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4">
        <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-id-card text-brand-600"></i> Datos del usuario</h3>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="120" autofocus
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="160"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Rol --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6">
        <h3 class="font-display font-bold text-ink-900 text-sm mb-3"><i class="fa-solid fa-user-shield text-brand-600"></i> Rol</h3>
        <label class="block text-sm font-medium text-ink-700 mb-1.5">Tipo de usuario</label>
        <select name="role" class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            <option value="student" @selected(old('role', $user->role) === 'student')>Estudiante</option>
            <option value="instructor" @selected(old('role', $user->role) === 'instructor')>Instructor</option>
        </select>
        @error('role')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        <p class="mt-2 text-xs text-ink-500 leading-relaxed">
            Para convertir a alguien en <strong>Superadmin</strong>, créalo desde
            <a href="{{ route('admin.admins.create') }}" class="text-brand-700 hover:underline">Administradores → Nuevo</a>
            (son cuentas en una tabla aparte).
        </p>
    </div>

    {{-- Contraseña + medidor --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 space-y-4"
         x-data="{
            pwd: '', show: false,
            get c() { return {
                len: this.pwd.length >= 8, lower: /[a-z]/.test(this.pwd), upper: /[A-Z]/.test(this.pwd),
                num: /[0-9]/.test(this.pwd), sym: /[^A-Za-z0-9]/.test(this.pwd),
            }},
            get score() { return Object.values(this.c).filter(Boolean).length },
            get pct() { return this.pwd.length === 0 ? 0 : Math.max(15, this.score * 20) },
            get label() { return this.pwd.length === 0 ? '' : (this.score <= 2 ? 'Débil' : (this.score <= 3 ? 'Aceptable' : (this.score === 4 ? 'Fuerte' : 'Muy fuerte'))) },
            get color() { return this.score <= 2 ? '#ef4444' : (this.score <= 3 ? '#f59e0b' : '#10B981') },
         }">
        <div class="flex items-center justify-between">
            <h3 class="font-display font-bold text-ink-900 text-sm"><i class="fa-solid fa-lock text-brand-600"></i> Contraseña</h3>
            <span class="text-xs text-ink-400">Déjala vacía para mantener la actual</span>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Nueva contraseña</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" x-model="pwd" autocomplete="new-password"
                    class="w-full px-4 py-3 pr-11 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 text-ink-400 hover:text-ink-700" tabindex="-1">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror

            <div x-show="pwd.length > 0" x-cloak class="mt-3">
                <div class="h-1.5 w-full rounded-full bg-ink-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300" :style="`width:${pct}%; background:${color}`"></div>
                </div>
                <p class="mt-1.5 text-xs font-semibold" :style="`color:${color}`" x-text="'Seguridad: ' + label"></p>
                <ul class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                    <template x-for="(req, key) in { len: 'Mínimo 8 caracteres', lower: 'Una minúscula', upper: 'Una mayúscula', num: 'Un número', sym: 'Un símbolo (recomendado)' }" :key="key">
                        <li class="flex items-center gap-1.5" :class="c[key] ? 'text-brand-600' : 'text-ink-400'">
                            <i class="fa-solid" :class="c[key] ? 'fa-circle-check' : 'fa-circle'"></i>
                            <span x-text="req"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Repite la contraseña</label>
            <input :type="show ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
            <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
        </button>
        <a href="{{ route('admin.users.show', $user) }}" class="px-5 py-3 rounded-2xl font-semibold bg-cream-2 text-ink-700 hover:bg-ink-100 transition">Cancelar</a>
    </div>
</form>

@endsection
