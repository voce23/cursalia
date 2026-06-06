<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Admin · Cursalia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2310B981'><path d='M4 7l8-4 8 4-8 4-8-4z'/></svg>">
</head>
<body class="font-sans antialiased text-white min-h-screen bg-ink-950 relative overflow-hidden">

    {{-- Decoración --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="blob bg-brand-600/30 w-[40rem] h-[40rem] -top-40 -left-40"></div>
        <div class="blob bg-coral-500/20 w-[30rem] h-[30rem] -bottom-20 -right-20"></div>
        <div class="blob bg-sun-400/15 w-[24rem] h-[24rem] top-1/3 -right-10"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 mb-8">
                <span class="grid place-items-center w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white shadow-soft">
                    <svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/></svg>
                </span>
                <div class="leading-tight">
                    <span class="block font-display font-extrabold text-2xl tracking-tight">Cursalia</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[0.22em] text-brand-300">Admin</span>
                </div>
            </a>

            {{-- Card --}}
            <div class="bg-white/[0.06] border border-white/10 backdrop-blur-xl rounded-3xl shadow-lift p-8">
                <span class="grid place-items-center w-12 h-12 rounded-2xl bg-brand-500/20 text-brand-300 border border-brand-500/30">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <h1 class="font-display font-extrabold text-2xl mt-5">Acceso restringido</h1>
                <p class="text-sm text-white/60 mt-1">Solo personal autorizado del equipo Cursalia.</p>

                @if ($errors->any())
                    <div class="mt-5 px-4 py-3 rounded-2xl bg-coral-500/15 border border-coral-500/30 text-coral-200 text-sm">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-white/85 mb-1.5" for="email">Correo</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40"><i class="fa-regular fa-envelope"></i></span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white/[0.05] border border-white/15 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition"
                                placeholder="admin@cursalia.test">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/85 mb-1.5" for="password">Contraseña</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/40"><i class="fa-solid fa-key"></i></span>
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-12 py-3 rounded-2xl bg-white/[0.05] border border-white/15 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 grid place-items-center w-8 h-8 rounded-xl text-white/40 hover:text-white hover:bg-white/10 transition">
                                <i class="fa-regular" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-white/75 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded-md bg-white/5 border-white/20 text-brand-500 focus:ring-brand-400">
                        Mantener sesión iniciada
                    </label>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-500 text-white hover:bg-brand-400 shadow-lift transition">
                        Entrar al panel <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-white/10 flex items-center gap-2 text-xs text-white/50">
                    <i class="fa-solid fa-shield-halved text-brand-400"></i>
                    Esta zona está protegida por rate-limiting y CSRF.
                </div>
            </div>

            <p class="text-center text-xs text-white/40 mt-6">
                <a href="{{ url('/') }}" class="hover:text-white"><i class="fa-solid fa-arrow-left text-[10px]"></i> Volver al sitio</a>
            </p>
        </div>
    </div>
</body>
</html>
