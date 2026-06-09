@extends('layouts.app')

@section('title', $contactSetting?->title ?: 'Contacto')
@section('description', 'Escríbenos para dudas, sugerencias o colaboraciones. Respondemos en menos de 48 horas hábiles. También tienes nuestro email y redes oficiales aquí.')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO Contacto
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    <div class="blob bg-coral-200 w-[26rem] h-[26rem] -top-20 -right-10"></div>
    <div class="blob bg-brand-200 w-[22rem] h-[22rem] top-32 -left-10"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10 text-center">
        <span class="sr inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-ink-200 shadow-soft text-xs font-semibold text-coral-500">
            <i class="fa-solid fa-paper-plane"></i> Contacto
        </span>
        <h1 class="sr s1 font-display font-extrabold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mt-6 text-ink-900">
            {{ $contactSetting?->title ?: 'Hablemos' }}
        </h1>
        @if ($contactSetting?->subtitle)
            <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-5 max-w-2xl mx-auto">{{ $contactSetting->subtitle }}</p>
        @else
            <p class="sr s2 text-ink-500 text-lg leading-relaxed mt-5 max-w-2xl mx-auto">¿Una duda, una idea, un curso que te gustaría que ofrezcamos? Escríbenos y te respondemos en menos de 24 horas.</p>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     TARJETAS DE CONTACTO
     ═══════════════════════════════════════════════════════════════════ --}}
@if ($contactCards->isNotEmpty())
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 pb-4">
    @php $cardColors = ['brand', 'coral', 'sun', 'brand']; @endphp
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($contactCards as $i => $card)
            @php $c = $cardColors[$i % 4]; @endphp
            <div class="card-lift sr s{{ ($i % 3) + 1 }} bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6">
                <span class="grid place-items-center w-12 h-12 rounded-2xl
                    @if($c === 'brand') bg-brand-100 text-brand-600
                    @elseif($c === 'coral') bg-coral-100 text-coral-500
                    @else bg-sun-100 text-sun-500 @endif">
                    <i class="{{ $card->icon ?: 'fa-solid fa-circle-info' }}"></i>
                </span>
                <h3 class="font-display font-bold text-ink-900 mt-4">{{ $card->title }}</h3>
                @if ($card->line_one)
                    <p class="text-sm text-ink-700 mt-1.5">{{ $card->line_one }}</p>
                @endif
                @if ($card->line_two)
                    <p class="text-sm text-ink-500">{{ $card->line_two }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     FORMULARIO + MAPA
     ═══════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid lg:grid-cols-[1fr_0.9fr] gap-8 lg:gap-12 items-start">

        {{-- Formulario --}}
        <div class="sr bg-white border border-ink-200/70 shadow-lift rounded-3xl p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-2xl text-ink-900">
                {{ $contactSetting?->form_title ?: 'Envíanos un mensaje' }}
            </h2>
            @if ($contactSetting?->form_subtitle)
                <p class="text-sm text-ink-500 mt-1">{{ $contactSetting->form_subtitle }}</p>
            @else
                <p class="text-sm text-ink-500 mt-1">Te responderemos a tu correo en menos de un día hábil.</p>
            @endif

            {{-- Mensajes de confirmación / error.
                 Clave propia 'contact_sent' (no 'success'): php-flasher intercepta
                 success/error como toasts y este layout no los renderiza. --}}
            @if (session('contact_sent'))
                <div id="contact-alert" class="mt-5 px-5 py-4 rounded-2xl bg-brand-50 border-2 border-brand-300 text-brand-800 flex items-start gap-3 shadow-soft">
                    <i class="fa-solid fa-circle-check text-brand-500 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-bold">¡Mensaje enviado correctamente! ✅</p>
                        <p class="text-sm text-brand-700/90 mt-0.5">¡Gracias! Tu mensaje fue enviado, te responderemos muy pronto.</p>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div id="contact-alert" class="mt-5 px-5 py-4 rounded-2xl bg-coral-50 border-2 border-coral-300 text-coral-800 flex items-start gap-3 shadow-soft">
                    <i class="fa-solid fa-circle-exclamation text-coral-500 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-bold">Revisa el formulario</p>
                        <p class="text-sm text-coral-700/90 mt-0.5">Faltan datos o hay algún campo incorrecto. Corrígelo e inténtalo de nuevo.</p>
                    </div>
                </div>
            @endif
            @if (session('contact_sent') || $errors->any())
                <script>
                    window.addEventListener('DOMContentLoaded', () => {
                        document.getElementById('contact-alert')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                </script>
            @endif

            <form method="POST" action="{{ route('contact.send') }}" class="mt-6 space-y-5">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5" for="name">Tu nombre</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-user"></i></span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="120"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                                placeholder="Tu nombre">
                        </div>
                        @error('name')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5" for="email">Correo</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-400"><i class="fa-regular fa-envelope"></i></span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                                placeholder="tu@correo.com">
                        </div>
                        @error('email')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="subject">Asunto</label>
                    <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required maxlength="150"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400"
                        placeholder="¿De qué quieres hablarnos?">
                    @error('subject')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="message">Mensaje</label>
                    <textarea id="message" name="message" required rows="5" maxlength="4000"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition placeholder-ink-400 resize-none"
                        placeholder="Cuéntanos lo que necesitas…">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                </div>

                <x-math-captcha label="Antes de enviar, demuéstranos que eres humano:" />

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-soft transition">
                    Enviar mensaje <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>

        {{-- Aside: mapa + horario --}}
        <aside class="sr s2 space-y-5">
            @if ($contactSetting?->map_embed_url)
                <div class="rounded-3xl overflow-hidden border border-ink-200/70 shadow-soft aspect-square sm:aspect-video lg:aspect-square">
                    <iframe src="{{ $contactSetting->map_embed_url }}" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @else
                <div class="relative rounded-3xl overflow-hidden border border-ink-200/70 shadow-soft aspect-square sm:aspect-video lg:aspect-square bg-gradient-to-br from-brand-400 to-brand-600 grid place-items-center text-white text-center p-6">
                    <div>
                        <i class="fa-solid fa-map-location-dot text-4xl"></i>
                        <p class="font-display font-extrabold text-xl mt-4">Te respondemos online</p>
                        <p class="text-brand-50/85 text-sm mt-2">Aún no tenemos oficinas físicas. Toda la comunicación es por correo.</p>
                    </div>
                </div>
            @endif

            {{-- Tarjeta horario --}}
            <div class="bg-white rounded-3xl border border-ink-200/70 shadow-soft p-6">
                <div class="flex items-center gap-3">
                    <span class="grid place-items-center w-11 h-11 rounded-2xl bg-sun-100 text-sun-500">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                    <div>
                        <p class="text-xs text-ink-500">Respondemos en</p>
                        <p class="font-display font-extrabold text-ink-900 text-lg">menos de 24 h</p>
                    </div>
                </div>
                <hr class="my-4 border-ink-200/70">
                <ul class="text-sm space-y-2">
                    <li class="flex items-center justify-between text-ink-700"><span>Lun – Vie</span><span class="font-semibold">9:00 – 18:00</span></li>
                    <li class="flex items-center justify-between text-ink-700"><span>Sábado</span><span class="font-semibold">10:00 – 14:00</span></li>
                    <li class="flex items-center justify-between text-ink-400"><span>Domingo</span><span class="font-semibold">cerrado</span></li>
                </ul>
            </div>
        </aside>
    </div>
</section>

@endsection
