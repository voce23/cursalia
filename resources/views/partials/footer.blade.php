{{-- ════════════════════════════════════════════════════════════════════
     FOOTER · 100% white-label dinámico desde DB.
     Lee Footer + FooterColumnOne/Two + SocialLink + CustomPage(legales).
     ════════════════════════════════════════════════════════════════════ --}}
@php
    $siteName  = $generalSetting->site_name ?? 'Cursalia';
    $logoPath  = $generalSetting->logo;
    $copyright = $generalSetting->copyright ?? '© '.date('Y').' '.$siteName.'. Todos los derechos reservados.';
@endphp

@if (($footerInfo->is_active ?? true))
<footer class="relative bg-white border-t border-ink-200/70 mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid sm:grid-cols-2 lg:grid-cols-12 gap-10">

            {{-- Marca --}}
            <div class="lg:col-span-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    @if ($logoPath)
                        <img loading="lazy" decoding="async" src="{{ asset('storage/'.$logoPath) }}" alt="{{ $siteName }}" class="h-9 w-auto">
                    @else
                        <span class="grid place-items-center w-9 h-9 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 text-white">
                            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l8-4 8 4-8 4-8-4z"/><path d="M4 7v6l8 4 8-4V7"/><path d="M12 11v10"/></svg>
                        </span>
                        <span class="font-display font-extrabold text-lg tracking-tight text-ink-900">{{ $siteName }}</span>
                    @endif
                </a>
                <p class="text-sm text-ink-500 leading-relaxed mt-4 max-w-xs">
                    {{ $footerInfo->description }}
                </p>

                {{-- Redes sociales --}}
                @if (count($socialLinks ?? []))
                    <div class="flex items-center gap-2 mt-5">
                        @foreach ($socialLinks as $s)
                            <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer"
                               class="grid place-items-center w-9 h-9 rounded-full bg-cream-2 text-ink-500 hover:bg-brand-100 hover:text-brand-700 transition"
                               aria-label="{{ $s['name'] }}">
                                <i class="{{ $s['icon'] }} text-sm"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Newsletter compacto --}}
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-6 max-w-xs">
                    @csrf
                    <label for="footer-email" class="text-xs font-semibold uppercase tracking-wider text-ink-400 block mb-2">Recibe novedades</label>
                    <div class="flex gap-2">
                        <input id="footer-email" type="email" name="email" required placeholder="tu@correo.com"
                            class="flex-1 px-3 py-2 rounded-full bg-cream-2 border border-ink-200 text-sm placeholder-ink-400 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                        <button type="submit" class="grid place-items-center w-10 h-10 rounded-full bg-brand-600 text-white hover:bg-brand-700 transition" aria-label="Suscribirme">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Columna 1 (Explorar) --}}
            @if (count($footerColumnOne ?? []))
                <div class="lg:col-span-2">
                    <h4 class="font-display font-bold text-sm uppercase tracking-wider text-ink-400">Explorar</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-ink-700">
                        @foreach ($footerColumnOne as $l)
                            <li><a href="{{ $l['url'] }}" class="hover:text-brand-700 transition">{{ $l['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Columna 2 (Soporte) --}}
            @if (count($footerColumnTwo ?? []))
                <div class="lg:col-span-2">
                    <h4 class="font-display font-bold text-sm uppercase tracking-wider text-ink-400">Soporte</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-ink-700">
                        @foreach ($footerColumnTwo as $l)
                            <li><a href="{{ $l['url'] }}" class="hover:text-brand-700 transition">{{ $l['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Legal (desde custom_pages) --}}
            @if (count($legalPages ?? []))
                <div class="lg:col-span-2">
                    <h4 class="font-display font-bold text-sm uppercase tracking-wider text-ink-400">Legal</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-ink-700">
                        @foreach ($legalPages as $p)
                            <li><a href="{{ $p['url'] }}" class="hover:text-brand-700 transition">{{ $p['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Contacto (del footerInfo) --}}
            <div class="lg:col-span-2">
                <h4 class="font-display font-bold text-sm uppercase tracking-wider text-ink-400">{{ $footerInfo->contact_title }}</h4>
                <ul class="mt-4 space-y-2.5 text-sm text-ink-700">
                    @if ($footerInfo->email)
                        <li class="flex items-start gap-2">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-brand-50 text-brand-600 shrink-0 mt-0.5">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 6l-10 7L2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                            </span>
                            <a href="mailto:{{ $footerInfo->email }}" class="hover:text-brand-700 transition break-all">{{ $footerInfo->email }}</a>
                        </li>
                    @endif
                    @if ($footerInfo->phone)
                        <li class="flex items-start gap-2">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-sun-100 text-sun-500 shrink-0 mt-0.5">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/></svg>
                            </span>
                            <a href="tel:{{ $footerInfo->phone }}" class="hover:text-brand-700 transition">{{ $footerInfo->phone }}</a>
                        </li>
                    @endif
                    @if ($footerInfo->address)
                        <li class="flex items-start gap-2">
                            <span class="grid place-items-center w-7 h-7 rounded-full bg-coral-100 text-coral-500 shrink-0 mt-0.5">
                                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </span>
                            {{ $footerInfo->address }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Línea inferior --}}
        <div class="mt-12 pt-6 border-t border-ink-200/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-ink-400">
            <p>{{ $copyright }}</p>
            @if ($footerInfo->bottom_text)
                {{-- SEGURIDAD: bottom_text se imprime como texto escapado (NO {!! !!})
                     para evitar XSS persistente si un admin malicioso o una sesión
                     comprometida intentara inyectar JS en el footer global. --}}
                <p class="text-ink-400">{{ $footerInfo->bottom_text }}</p>
            @endif
        </div>
    </div>
</footer>
@endif
