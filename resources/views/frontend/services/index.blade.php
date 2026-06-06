@extends('layouts.app')

@section('title', 'Servicios y Asesoría')
@section('description', 'Cursalia FREE es gratis. Te ayudamos a instalarlo, personalizarlo o resolver tus dudas técnicas con planes claros.')

@section('content')

@php
    $whatsappRaw  = preg_replace('/\D+/', '', $generalSetting->whatsapp_number ?? '');
    $whatsappMsg  = urlencode($generalSetting->whatsapp_default_message ?? 'Hola Cursalia, me interesa…');
    $whatsappLink = $whatsappRaw ? "https://wa.me/{$whatsappRaw}?text={$whatsappMsg}" : null;
    $servicesEmail = $generalSetting->services_email ?? $footerInfo->email ?? null;
@endphp

{{-- ═══════════════════════════════════════════════════════════════════
     HERO con propuesta clara
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-brand-200 w-[26rem] h-[26rem] -top-20 -left-10"></div>
    <div class="blob bg-sun-200 w-[22rem] h-[22rem] top-32 -right-10"></div>
    <div class="blob bg-coral-200 w-[18rem] h-[18rem] top-60 left-1/3"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10 text-center">
        <span class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-brand-700">
            <i class="fa-solid fa-handshake-angle text-coral-500"></i> Servicios y asesoría
        </span>
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-6 text-ink-900">
            El producto es <span class="text-brand-600">gratis</span>.<br>
            Nuestro <span class="text-coral-500">tiempo</span> se cobra.
        </h1>
        <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-6 max-w-2xl mx-auto">
            <strong class="text-ink-900">Cursalia FREE</strong> es descargable y libre. Si necesitas asesoría, instalación o personalización, elige el plan que mejor se ajuste y nuestro equipo te acompaña.
        </p>
        <p class="sr s3 mt-4 text-xs text-ink-400 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-shield-halved text-brand-500"></i>
            Sin sorpresas: <strong class="text-ink-700">cada plan dice exactamente qué incluye y qué no</strong>
        </p>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     4 PLANES de servicio
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 items-stretch">
        @foreach ($services as $svc)
            @php
                $color = $svc->color ?? '#10B981';
                $rgba = [
                    'tint'   => $color.'1A',
                    'border' => $color.'33',
                ];
            @endphp
            <div class="card-lift sr s{{ ($loop->index % 4) + 1 }} relative bg-white rounded-3xl border shadow-soft p-6 flex flex-col"
                 style="border-color: {{ $svc->is_featured ? $color : '#E6E7F0' }}; {{ $svc->is_featured ? 'box-shadow: 0 14px 40px -18px '.$color.'66;' : '' }}">

                @if ($svc->badge_text)
                    <span class="absolute -top-3 left-5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white shadow-soft"
                          style="background: {{ $color }}">
                        <i class="fa-solid fa-star text-[9px]"></i> {{ $svc->badge_text }}
                    </span>
                @endif

                <span class="grid place-items-center w-12 h-12 rounded-2xl shrink-0"
                      style="background: {{ $rgba['tint'] }}; color: {{ $color }}">
                    <i class="{{ $svc->icon ?: 'fa-solid fa-handshake-angle' }} text-lg"></i>
                </span>

                <h3 class="font-display font-extrabold text-lg text-ink-900 mt-4">{{ $svc->title }}</h3>
                @if ($svc->headline)
                    <p class="text-xs text-ink-500 mt-1.5 leading-relaxed">{{ $svc->headline }}</p>
                @endif

                <div class="mt-4 pb-4 border-b border-ink-200/70">
                    @if ($svc->is_free)
                        <span class="font-display font-extrabold text-3xl text-brand-600">Gratis</span>
                    @else
                        <span class="font-display font-extrabold text-3xl text-ink-900">${{ number_format($svc->price, 0) }}</span>
                        <span class="text-xs text-ink-500 ml-1">{{ $svc->currency }}</span>
                    @endif
                    @if ($svc->price_suffix)
                        <p class="text-[10px] font-bold uppercase tracking-wider text-ink-400 mt-0.5">{{ $svc->price_suffix }}</p>
                    @endif
                </div>

                @if ($svc->features)
                    <ul class="mt-4 space-y-2 flex-1">
                        @foreach ($svc->features as $f)
                            <li class="flex items-start gap-2 text-xs text-ink-700">
                                <i class="fa-solid fa-check text-[10px] mt-1 shrink-0" style="color: {{ $color }}"></i>
                                <span>{{ $f }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($svc->cta_url)
                    <a href="{{ $svc->cta_url }}" target="_blank" rel="noopener"
                       class="mt-5 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-sm transition"
                       style="background: {{ $color }}; color: #fff">
                        {{ $svc->cta_text }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                @else
                    <a href="#formulario" onclick="document.getElementById('service_id').value={{ $svc->id }}"
                       class="mt-5 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-2xl font-bold text-sm text-white transition hover:opacity-90"
                       style="background: {{ $color }}">
                        {{ $svc->cta_text }} <i class="fa-solid fa-arrow-down text-xs"></i>
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     ¿Cómo elegir? + Contacto rápido (WhatsApp + Email)
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid lg:grid-cols-[1.2fr_1fr] gap-8 items-start">

        {{-- ¿Cómo elegir? --}}
        <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-7">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold uppercase tracking-wider">
                <i class="fa-solid fa-compass"></i> Cómo elegir
            </span>
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-ink-900 mt-4">¿Qué plan es para ti?</h2>

            @php $guide = [
                ['Sé programar y solo necesito los docs',                    'Documentación', '#10B981'],
                ['Tengo dudas concretas, prefiero hablar con alguien',       'Asesoría 1 h ($29)', '#FBBF24'],
                ['No quiero tocar nada técnico, hazlo todo tú',              'Instalación llave en mano ($97)', '#FB7185'],
                ['Quiero MI marca aplicada al sitio, no solo instalación',   'Personalización completa ($297)', '#3E6CF6'],
            ]; @endphp

            <ul class="mt-6 space-y-3">
                @foreach ($guide as [$question, $plan, $color])
                    <li class="flex items-start gap-3 p-3 rounded-2xl bg-cream-2 border border-ink-200/70">
                        <span class="grid place-items-center w-9 h-9 rounded-xl text-white text-sm font-bold shrink-0" style="background: {{ $color }}">{{ $loop->iteration }}</span>
                        <div class="flex-1">
                            <p class="text-sm text-ink-700">{{ $question }}</p>
                            <p class="text-xs font-bold mt-0.5" style="color: {{ $color }}">→ {{ $plan }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Contacto rápido --}}
        <div class="bg-gradient-to-br from-brand-500 to-brand-700 rounded-3xl text-white p-7 shadow-lift relative overflow-hidden">
            <div class="blob bg-sun-300/40 w-56 h-56 -top-10 -right-10"></div>
            <div class="relative">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur text-xs font-semibold">
                    <i class="fa-solid fa-bolt text-sun-300"></i> Más rápido todavía
                </span>
                <h2 class="font-display font-extrabold text-2xl mt-4">¿Prefieres escribirnos directo?</h2>
                <p class="text-brand-50/90 mt-3">Estamos disponibles de lunes a viernes, 9-18h. Respondemos en menos de un día hábil.</p>

                <div class="mt-6 space-y-3">
                    @if ($whatsappLink)
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white text-ink-900 hover:scale-[1.02] transition font-bold">
                            <span class="grid place-items-center w-10 h-10 rounded-2xl bg-[#25D366] text-white">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </span>
                            <span class="flex-1 text-left">
                                <span class="block text-sm">WhatsApp</span>
                                <span class="block text-xs text-ink-500">+{{ $whatsappRaw }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-right text-xs text-ink-400"></i>
                        </a>
                    @endif
                    @if ($servicesEmail)
                        <a href="mailto:{{ $servicesEmail }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur hover:bg-white/15 transition font-semibold">
                            <span class="grid place-items-center w-10 h-10 rounded-2xl bg-sun-400 text-ink-900">
                                <i class="fa-regular fa-envelope text-lg"></i>
                            </span>
                            <span class="flex-1 text-left">
                                <span class="block text-sm">Email de servicios</span>
                                <span class="block text-xs text-white/70">{{ $servicesEmail }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-right text-xs text-white/50"></i>
                        </a>
                    @endif
                </div>

                <p class="text-xs text-brand-50/70 mt-5">
                    <i class="fa-solid fa-circle-info"></i>
                    Si no te corre prisa, prefiere el formulario de abajo: queda registrado y te respondemos con todo el detalle.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     FORMULARIO de pedido (anchor #formulario)
     ═══════════════════════════════════════════════════════════════════ --}}
<section id="formulario" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="bg-white rounded-[2.5rem] border border-ink-200/70 shadow-lift p-7 sm:p-10">
        <div class="text-center max-w-xl mx-auto mb-8">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-coral-100 text-coral-600 text-xs font-semibold uppercase tracking-wider">
                <i class="fa-regular fa-clipboard"></i> Solicitar servicio
            </span>
            <h2 class="font-display font-extrabold text-3xl text-ink-900 mt-4">Cuéntanos tu caso</h2>
            <p class="text-ink-500 mt-3">Selecciona el plan que más se acerca, déjanos cómo prefieres que te contactemos y te respondemos en menos de 24 horas.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 px-5 py-4 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 px-5 py-4 rounded-2xl bg-coral-50 border border-coral-200 text-coral-700">
                <p class="font-bold flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i> Revisa los campos:</p>
                <ul class="text-sm mt-2 ml-6 list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('services.request') }}" class="space-y-5" x-data="{ pref: '{{ old('contact_preference', 'email') }}' }">
            @csrf

            {{-- Servicio --}}
            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="service_id">¿Qué servicio te interesa?</label>
                <select id="service_id" name="service_id"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                    <option value="">— Aún no estoy seguro —</option>
                    @foreach ($services as $svc)
                        <option value="{{ $svc->id }}"
                                @selected(old('service_id', $preselect && (string) $preselect === $svc->slug ? $svc->id : null) == $svc->id)>
                            {{ $svc->title }} @if (! $svc->is_free)— ${{ number_format($svc->price, 0) }}@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="name">Tu nombre</label>
                    <input id="name" type="text" name="name" required maxlength="120" value="{{ old('name', auth()->user()->name ?? '') }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Email</label>
                    <input id="email" type="email" name="email" required maxlength="255" value="{{ old('email', auth()->user()->email ?? '') }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="whatsapp">WhatsApp (opcional)</label>
                    <input id="whatsapp" type="tel" name="whatsapp" maxlength="32" value="{{ old('whatsapp') }}"
                        placeholder="+34 600 00 00 00"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="budget">Presupuesto</label>
                    <select id="budget" name="budget" class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
                        <option value="">— Lo veremos juntos —</option>
                        @foreach (['< $50','$50–$100','$100–$300','$300–$1000','> $1000'] as $r)
                            <option value="{{ $r }}" @selected(old('budget') === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Preferencia de contacto --}}
            <div>
                <span class="block text-sm font-medium text-ink-700 mb-2">¿Cómo prefieres que te contactemos?</span>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([
                        ['email',    'fa-regular fa-envelope',   'Email'],
                        ['whatsapp', 'fa-brands fa-whatsapp',    'WhatsApp'],
                        ['both',     'fa-solid fa-shuffle',      'Ambos'],
                    ] as [$val, $ic, $lbl])
                        <label class="cursor-pointer">
                            <input type="radio" name="contact_preference" value="{{ $val }}" x-model="pref" class="sr-only peer">
                            <div class="px-3 py-3 rounded-2xl border-2 text-center transition"
                                 :class="pref === '{{ $val }}' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-ink-200 bg-cream-2 text-ink-700 hover:border-ink-300'">
                                <i class="{{ $ic }} text-base"></i>
                                <p class="text-xs font-bold mt-1">{{ $lbl }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="subject">Asunto breve (opcional)</label>
                <input id="subject" type="text" name="subject" maxlength="200" value="{{ old('subject') }}"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5" for="message">Cuéntanos qué necesitas <span class="text-coral-500">*</span></label>
                <textarea id="message" name="message" required minlength="10" maxlength="4000" rows="5"
                    class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:bg-white text-sm resize-none"
                    placeholder="Por ejemplo: «Quiero abrir una academia de cocina y necesito el sitio listo en 2 semanas con mi logo aplicado…»">{{ old('message') }}</textarea>
            </div>

            <x-math-captcha label="Antes de enviar, demuéstranos que eres humano:" />

            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-ink-200/70">
                <p class="text-xs text-ink-400 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-brand-500"></i>
                    Tus datos solo se usan para responderte. Lee la <a href="{{ url('/legal/privacy') }}" class="underline">política de privacidad</a>.
                </p>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                    Enviar solicitud <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>
        </form>
    </div>
</section>

@endsection
