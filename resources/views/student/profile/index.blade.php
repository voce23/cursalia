@extends('layouts.dashboard')

@section('title', 'Mi perfil')
@section('page-title', 'Mi perfil')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ tab: '{{ session('active_tab', 'info') }}' }">

    {{-- Pestañas --}}
    <div class="inline-flex items-center gap-1 bg-white border border-ink-200/70 rounded-full p-1 shadow-soft mb-6">
        <button @click="tab='info'" :class="tab==='info' ? 'bg-brand-600 text-white shadow-soft' : 'text-ink-500 hover:text-ink-800'"
                class="px-5 py-2 rounded-full text-sm font-semibold transition"><i class="fa-solid fa-user mr-1.5"></i> Datos</button>
        <button @click="tab='password'" :class="tab==='password' ? 'bg-brand-600 text-white shadow-soft' : 'text-ink-500 hover:text-ink-800'"
                class="px-5 py-2 rounded-full text-sm font-semibold transition"><i class="fa-solid fa-lock mr-1.5"></i> Contraseña</button>
    </div>

    {{-- ───────── Pestaña DATOS ───────── --}}
    <div x-show="tab==='info'">
        @if (session('profile_success'))
            <div class="mb-5 rounded-2xl bg-brand-50 border border-brand-200 text-brand-800 px-4 py-3 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('profile_success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data"
              class="bg-white rounded-3xl border border-ink-200/70 p-7 sm:p-8 shadow-soft space-y-5">
            @csrf

            {{-- Avatar --}}
            <div class="flex items-center gap-5">
                <img src="{{ $user->image ? asset('storage/'.$user->image) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=10B981&color=fff&size=128' }}"
                     alt="Avatar" class="w-20 h-20 rounded-2xl object-cover border border-ink-200">
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Foto de perfil</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-brand-700 file:font-semibold hover:file:bg-brand-100 cursor-pointer">
                    <p class="mt-1 text-xs text-ink-400">JPG, PNG o WebP · máx 2 MB.</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Nombre <span class="text-coral-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Email <span class="text-coral-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Titular / profesión</label>
                    <input type="text" name="headline" value="{{ old('headline', $user->headline) }}" maxlength="255" placeholder="Ej. Diseñadora UX"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="50"
                           class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-ink-700 mb-1.5">Género</label>
                    <select name="gender" class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
                        <option value="">— Prefiero no decir —</option>
                        <option value="male" @selected(old('gender', $user->gender) === 'male')>Hombre</option>
                        <option value="female" @selected(old('gender', $user->gender) === 'female')>Mujer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Sobre ti</label>
                <textarea name="bio" rows="4" maxlength="6000" placeholder="Cuéntanos algo sobre ti…"
                          class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-soft hover:bg-brand-700 transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
        </form>
    </div>

    {{-- ───────── Pestaña CONTRASEÑA ───────── --}}
    <div x-show="tab==='password'" x-cloak>
        @if (session('password_success'))
            <div class="mb-5 rounded-2xl bg-brand-50 border border-brand-200 text-brand-800 px-4 py-3 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('password_success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.update-password') }}"
              class="bg-white rounded-3xl border border-ink-200/70 p-7 sm:p-8 shadow-soft space-y-5 max-w-lg">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Contraseña actual <span class="text-coral-500">*</span></label>
                <input type="password" name="current_password" required
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Nueva contraseña <span class="text-coral-500">*</span></label>
                <input type="password" name="password" required
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-ink-700 mb-1.5">Repite la nueva contraseña <span class="text-coral-500">*</span></label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-xl border border-ink-200 px-4 py-2.5 focus:border-brand-400 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>

            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold shadow-soft hover:bg-brand-700 transition">
                <i class="fa-solid fa-key"></i> Cambiar contraseña
            </button>
        </form>
    </div>

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div class="mt-5 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
