@extends('layouts.admin')

@section('title', 'Mi perfil')
@section('page-title', 'Mi perfil')
@section('page-subtitle', 'Tu identidad como autor del blog (E-E-A-T para Google)')

@section('content')

<div x-data="{ tab: '{{ session('active_tab', 'info') }}' }" class="space-y-6">

    {{-- Tabs --}}
    <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-2 flex flex-wrap gap-1.5">
        <button type="button" @click="tab='info'"
                :class="tab==='info' ? 'bg-brand-100 text-brand-700' : 'text-ink-600 hover:bg-cream-2'"
                class="px-4 py-2 rounded-2xl text-sm font-semibold transition flex items-center gap-2">
            <i class="fa-solid fa-user"></i> Datos básicos
        </button>
        <button type="button" @click="tab='author'"
                :class="tab==='author' ? 'bg-brand-100 text-brand-700' : 'text-ink-600 hover:bg-cream-2'"
                class="px-4 py-2 rounded-2xl text-sm font-semibold transition flex items-center gap-2">
            <i class="fa-solid fa-id-badge"></i> Autor · SEO
        </button>
        <button type="button" @click="tab='password'"
                :class="tab==='password' ? 'bg-brand-100 text-brand-700' : 'text-ink-600 hover:bg-cream-2'"
                class="px-4 py-2 rounded-2xl text-sm font-semibold transition flex items-center gap-2">
            <i class="fa-solid fa-lock"></i> Contraseña
        </button>
    </div>

    {{-- Flash perfil --}}
    @if (session('profile_success'))
        <div x-show="tab==='info' || tab==='author'" class="px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('profile_success') }}
        </div>
    @endif
    @if (session('password_success'))
        <div x-show="tab==='password'" class="px-4 py-3 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('password_success') }}
        </div>
    @endif

    {{-- ───────────── DATOS BÁSICOS ───────────── --}}
    <form x-show="tab==='info'" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
          class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid sm:grid-cols-[140px_1fr] gap-5 items-start">
            <div class="text-center">
                @if ($admin->image)
                    <img src="{{ asset('storage/'.$admin->image) }}" alt="Foto" class="w-28 h-28 rounded-2xl object-cover mx-auto shadow-soft">
                @else
                    <span class="grid place-items-center w-28 h-28 rounded-2xl bg-gradient-to-br from-brand-400 to-coral-400 text-white font-bold text-3xl mx-auto shadow-soft">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </span>
                @endif
                <label class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cream-2 hover:bg-brand-50 text-ink-700 text-xs font-semibold cursor-pointer transition">
                    <i class="fa-solid fa-camera"></i> Cambiar foto
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                </label>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                    @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                    @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Inputs hidden para campos del otro tab, para no perderlos al guardar este form --}}
        <input type="hidden" name="bio" value="{{ old('bio', $admin->bio) }}">
        <input type="hidden" name="headline" value="{{ old('headline', $admin->headline) }}">
        <input type="hidden" name="social_x" value="{{ old('social_x', $admin->social_x) }}">
        <input type="hidden" name="social_linkedin" value="{{ old('social_linkedin', $admin->social_linkedin) }}">
        <input type="hidden" name="social_github" value="{{ old('social_github', $admin->social_github) }}">
        <input type="hidden" name="social_youtube" value="{{ old('social_youtube', $admin->social_youtube) }}">
        <input type="hidden" name="social_web" value="{{ old('social_web', $admin->social_web) }}">

        <div class="flex justify-end pt-4 border-t border-ink-200/70">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar datos
            </button>
        </div>
    </form>

    {{-- ───────────── AUTOR · E-E-A-T (SEO) ───────────── --}}
    <form x-show="tab==='author'" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
          class="space-y-5" x-cloak>
        @csrf
        {{-- Inputs hidden para campos del otro tab --}}
        <input type="hidden" name="name" value="{{ old('name', $admin->name) }}">
        <input type="hidden" name="email" value="{{ old('email', $admin->email) }}">

        {{-- Explicación E-E-A-T --}}
        <div class="bg-gradient-to-br from-brand-50 via-white to-cream border border-brand-200 rounded-3xl p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <span class="grid place-items-center w-10 h-10 rounded-2xl bg-brand-600 text-white shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </span>
                <div class="flex-1">
                    <p class="font-display font-bold text-ink-900">¿Por qué importan estos datos?</p>
                    <p class="text-sm text-ink-700 mt-1.5 leading-relaxed">
                        Google evalúa cada artículo con un criterio llamado <strong>E-E-A-T</strong>: <em>Experience, Expertise, Authoritativeness, Trust</em>.
                        Sin un autor identificable con bio, foto y redes verificables, asume que tu contenido es de "fuente desconocida" y lo posiciona peor.
                        Con estos datos rellenos, Cursalia emite un Schema.org <code class="bg-cream-2 px-1.5 py-0.5 rounded">Person</code> + <code class="bg-cream-2 px-1.5 py-0.5 rounded">Article.author</code> en cada post y aparece tu byline en los resultados de búsqueda.
                    </p>
                </div>
            </div>
        </div>

        {{-- Headline --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-5">
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">
                    Línea de presentación (headline)
                    <span class="text-ink-400 font-normal">· una frase que defina quién eres</span>
                </label>
                <input type="text" name="headline" value="{{ old('headline', $admin->headline) }}" maxlength="180"
                       placeholder="Ej: Desarrollador Laravel desde 2015, creador de Cursalia"
                       class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
                <p class="text-xs text-ink-400 mt-1.5">Se mostrará bajo tu nombre en cada artículo y en /sobre-el-autor.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">
                    Biografía
                    <span class="text-ink-400 font-normal">· quién eres, qué haces, por qué la gente debería confiar en ti</span>
                </label>
                <textarea name="bio" rows="6" maxlength="6000"
                          placeholder="Ej: Llevo 12 años construyendo plataformas educativas online. He visto cientos de profesores rendirse ante Hotmart. Creé Cursalia para que cualquiera con paciencia tenga su propia academia sin depender de nadie."
                          class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-y leading-relaxed">{{ old('bio', $admin->bio) }}</textarea>
                <p class="text-xs text-ink-400 mt-1.5">Recomendado: 400–800 caracteres. Habla en primera persona, con datos concretos.</p>
            </div>
        </section>

        {{-- Redes oficiales --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-4">
            <div>
                <p class="font-display font-bold text-ink-900">Redes oficiales <span class="text-ink-400 font-normal text-sm">(para Schema.org Person.sameAs)</span></p>
                <p class="text-xs text-ink-500 mt-1">Pega las URLs <strong>completas</strong> de tus perfiles. Solo los reales y activos: Google verifica que existan.</p>
            </div>

            @php
                $socials = [
                    ['social_x',        'fa-brands fa-x-twitter',  'X / Twitter',   'https://x.com/tuusuario'],
                    ['social_linkedin', 'fa-brands fa-linkedin',   'LinkedIn',      'https://linkedin.com/in/tuusuario'],
                    ['social_github',   'fa-brands fa-github',     'GitHub',        'https://github.com/tuusuario'],
                    ['social_youtube',  'fa-brands fa-youtube',    'YouTube',       'https://youtube.com/@tucanal'],
                    ['social_web',      'fa-solid fa-globe',       'Tu web/blog',   'https://tusitio.com'],
                ];
            @endphp

            <div class="grid gap-3">
                @foreach ($socials as [$field, $icon, $label, $placeholder])
                    <div class="flex items-center gap-3 bg-cream-2 border border-ink-200 rounded-2xl px-4 py-2.5 focus-within:ring-2 focus-within:ring-brand-400 focus-within:bg-white transition">
                        <i class="{{ $icon }} text-ink-500 w-5 text-center"></i>
                        <span class="text-xs font-semibold text-ink-600 w-24 shrink-0">{{ $label }}</span>
                        <input type="url" name="{{ $field }}" value="{{ old($field, $admin->$field) }}"
                               placeholder="{{ $placeholder }}"
                               class="flex-1 bg-transparent border-0 focus:ring-0 focus:outline-none text-sm">
                        @error($field)<p class="text-xs text-coral-500">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </section>

        <div class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 flex flex-wrap items-center gap-3 justify-between">
            <p class="text-sm text-ink-500">
                <i class="fa-solid fa-eye text-ink-400"></i>
                Estos datos aparecerán en <a href="/sobre-el-autor" target="_blank" class="text-brand-700 font-semibold hover:underline">/sobre-el-autor</a> y al pie de cada artículo.
            </p>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar datos del autor
            </button>
        </div>
    </form>

    {{-- ───────────── CONTRASEÑA ───────────── --}}
    <form x-show="tab==='password'" method="POST" action="{{ route('admin.profile.update-password') }}"
          class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8 space-y-5" x-cloak>
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Contraseña actual</label>
            <input type="password" name="current_password" required
                   class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
            @error('current_password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Nueva contraseña</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
            @error('password')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink-700 mb-1.5">Repite la nueva contraseña</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white">
        </div>
        <div class="flex justify-end pt-4 border-t border-ink-200/70">
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold transition">
                <i class="fa-solid fa-key"></i> Cambiar contraseña
            </button>
        </div>
    </form>
</div>

@endsection
