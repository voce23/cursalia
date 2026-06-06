@extends('layouts.admin')

@section('title', 'Apariencia')
@section('page-title', 'Apariencia de tu sitio')
@section('page-subtitle', 'Marca, colores, tipografía y secciones — todo sin tocar código')

@section('content')

<div x-data="appearance(@js([
    'brand'  => $setting->brand_color  ?? '#10B981',
    'accent' => $setting->accent_color ?? '#FB7185',
    'sun'    => $setting->sun_color    ?? '#FBBF24',
    'ink'    => $setting->ink_color    ?? '#1F2933',
    'preset' => $setting->theme_preset ?? 'cursalia-green',
]))" class="space-y-6">

    {{-- ════════════════════ Paletas predefinidas (1 click) ════════════════════ --}}
    <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
        <div class="flex items-start justify-between gap-3 mb-5">
            <div>
                <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2">
                    <i class="fa-solid fa-palette text-brand-600"></i> Paleta de marca
                </h2>
                <p class="text-sm text-ink-500 mt-1">Elige una paleta predefinida o personaliza cada color.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach ($presets as $key => $p)
                <form method="POST" action="{{ route('admin.appearance.preset') }}">
                    @csrf
                    <input type="hidden" name="preset" value="{{ $key }}">
                    <button type="submit"
                            class="w-full text-left p-4 rounded-3xl border-2 transition card-lift
                                   {{ $setting->theme_preset === $key ? 'border-brand-500 bg-brand-50' : 'border-ink-200 bg-white hover:border-ink-300' }}">
                        <div class="flex gap-1.5">
                            <span class="w-7 h-7 rounded-full" style="background: {{ $p['brand'] }}"></span>
                            <span class="w-7 h-7 rounded-full" style="background: {{ $p['accent'] }}"></span>
                            <span class="w-7 h-7 rounded-full" style="background: {{ $p['sun'] }}"></span>
                            <span class="w-7 h-7 rounded-full" style="background: {{ $p['ink'] }}"></span>
                        </div>
                        <p class="font-display font-bold text-sm text-ink-900 mt-3">{{ $p['label'] }}</p>
                        @if ($setting->theme_preset === $key)
                            <p class="inline-flex items-center gap-1 text-[10px] font-bold text-brand-700 mt-1 uppercase">
                                <i class="fa-solid fa-circle-check"></i> Activa
                            </p>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </section>

    {{-- ════════════════════ Formulario principal ════════════════════ --}}
    <form method="POST" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ─────────── Marca ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-5">
                <i class="fa-solid fa-tag text-coral-500"></i> Identidad
            </h2>

            <div class="grid lg:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="site_name">Nombre del sitio</label>
                    <input id="site_name" type="text" name="site_name" value="{{ old('site_name', $setting->site_name) }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="default_locale">Idioma por defecto</label>
                    <select id="default_locale" name="default_locale"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                        <option value="es" @selected($setting->default_locale === 'es')>Español</option>
                        <option value="en" @selected($setting->default_locale === 'en')>English</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="site_slogan">Eslogan</label>
                    <input id="site_slogan" type="text" name="site_slogan" value="{{ old('site_slogan', $setting->site_slogan) }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="seo_default_description">Meta descripción SEO (fallback)</label>
                    <textarea id="seo_default_description" name="seo_default_description" rows="2" maxlength="320"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition resize-none">{{ old('seo_default_description', $setting->seo_default_description) }}</textarea>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="copyright">Copyright (pie de página)</label>
                    <input id="copyright" type="text" name="copyright" value="{{ old('copyright', $setting->copyright) }}"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                </div>
            </div>
        </section>

        {{-- ─────────── Logo / Favicon / OG ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-5">
                <i class="fa-solid fa-image text-sun-500"></i> Imágenes de marca
            </h2>

            <div class="grid sm:grid-cols-3 gap-5">
                @foreach ([
                    ['logo',     'Logo',     'Recomendado: SVG transparente o PNG 240×64.', 'h-12'],
                    ['favicon',  'Favicon',  'PNG o ICO 32×32 o 64×64.',                      'w-12 h-12'],
                    ['og_image', 'OG image', 'PNG o JPG 1200×630 para vistas previas en redes.', 'w-full aspect-[1200/630]'],
                ] as [$field, $label, $help, $previewClass])
                    <div>
                        <label class="block text-sm font-medium text-ink-700 mb-1.5" for="{{ $field }}">{{ $label }}</label>
                        <div class="bg-cream-2 border border-ink-200 rounded-2xl p-3 text-center">
                            @if ($setting->{$field})
                                <img loading="lazy" src="{{ asset('storage/'.$setting->{$field}) }}" alt="{{ $label }}"
                                     class="{{ $previewClass }} mx-auto bg-white rounded-xl object-contain shadow-soft">
                            @else
                                <span class="grid place-items-center {{ $previewClass }} mx-auto bg-ink-100 text-ink-400 rounded-xl">
                                    <i class="fa-regular fa-image text-2xl"></i>
                                </span>
                            @endif
                            <input id="{{ $field }}" type="file" name="{{ $field }}" accept="image/*"
                                class="mt-3 w-full text-xs text-ink-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:bg-brand-100 file:text-brand-700 file:font-semibold file:cursor-pointer">
                        </div>
                        <p class="text-xs text-ink-400 mt-1.5">{{ $help }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────── SEO · Buscadores y analítica ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-1">
                <i class="fa-brands fa-google text-brand-600"></i> SEO · Buscadores y analítica
            </h2>
            <p class="text-sm text-ink-500 mb-5">Conecta tu sitio con Google y Bing para que indexen tu contenido rápido y veas el tráfico que llega.</p>

            <div class="space-y-5">
                {{-- GOOGLE SEARCH CONSOLE --}}
                <details class="rounded-3xl border-2 border-brand-200 bg-brand-50/40 overflow-hidden group" {{ empty($setting->google_site_verification) ? 'open' : '' }}>
                    <summary class="cursor-pointer px-5 py-4 flex items-center gap-3 hover:bg-brand-50 transition">
                        <span class="grid place-items-center w-9 h-9 rounded-2xl bg-white border border-brand-200 shrink-0">
                            <i class="fa-brands fa-google text-brand-700"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-display font-bold text-ink-900 text-sm">Google Search Console</p>
                            <p class="text-xs text-ink-500">Verifica tu propiedad y envía el sitemap para que Google te indexe en 24–72h.</p>
                        </div>
                        @if (!empty($setting->google_site_verification))
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-brand-600 text-white px-2 py-0.5 rounded-full">Activo</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-ink-400 text-xs group-open:rotate-180 transition"></i>
                    </summary>
                    <div class="px-5 pb-5 pt-3 border-t border-brand-200/70 space-y-4">
                        <ol class="text-sm text-ink-700 space-y-2 list-decimal list-inside leading-relaxed">
                            <li>Entra en <a href="https://search.google.com/search-console" target="_blank" class="text-brand-700 font-semibold hover:underline">search.google.com/search-console</a> con tu cuenta de Google.</li>
                            <li>Pulsa <strong>"Añadir propiedad"</strong> → tipo <strong>"Prefijo de URL"</strong> → pega tu dominio completo (ej: <code class="bg-cream-2 px-1.5 py-0.5 rounded">https://cursalia.com</code>).</li>
                            <li>Elige el método de verificación <strong>"Etiqueta HTML"</strong>. Te darán algo como:<br>
                                <code class="block bg-cream-2 px-3 py-2 rounded-xl mt-1 text-xs break-all">&lt;meta name="google-site-verification" content="<span class="text-brand-700 font-bold">ABCD1234…</span>"&gt;</code>
                            </li>
                            <li>Copia <strong>solo el valor</strong> (lo que va entre comillas en <code>content=""</code>) y pégalo aquí abajo.</li>
                            <li>Guarda y vuelve a Search Console → pulsa <strong>"Verificar"</strong>.</li>
                            <li>En Search Console → <strong>Sitemaps</strong> → añade <code class="bg-cream-2 px-1.5 py-0.5 rounded">sitemap.xml</code>.</li>
                        </ol>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1.5">Código de verificación</label>
                            <input type="text" name="google_site_verification" value="{{ old('google_site_verification', $setting->google_site_verification) }}"
                                   placeholder="Pega aquí solo el valor del content (sin comillas, sin meta tag)"
                                   class="w-full px-4 py-3 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-sm font-mono">
                            @error('google_site_verification')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </details>

                {{-- BING WEBMASTER --}}
                <details class="rounded-3xl border-2 border-sun-200 bg-sun-50/40 overflow-hidden group" {{ empty($setting->bing_site_verification) ? '' : '' }}>
                    <summary class="cursor-pointer px-5 py-4 flex items-center gap-3 hover:bg-sun-50 transition">
                        <span class="grid place-items-center w-9 h-9 rounded-2xl bg-white border border-sun-300 shrink-0">
                            <i class="fa-brands fa-microsoft text-sun-700"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-display font-bold text-ink-900 text-sm">Bing Webmaster Tools</p>
                            <p class="text-xs text-ink-500">Bing manda más tráfico del que crees y es 10× más fácil rankear ahí. Conéctalo igual.</p>
                        </div>
                        @if (!empty($setting->bing_site_verification))
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-sun-600 text-white px-2 py-0.5 rounded-full">Activo</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-ink-400 text-xs group-open:rotate-180 transition"></i>
                    </summary>
                    <div class="px-5 pb-5 pt-3 border-t border-sun-300/70 space-y-4">
                        <ol class="text-sm text-ink-700 space-y-2 list-decimal list-inside leading-relaxed">
                            <li>Entra en <a href="https://www.bing.com/webmasters" target="_blank" class="text-sun-700 font-semibold hover:underline">bing.com/webmasters</a>.</li>
                            <li>Tip: si ya tienes Google Search Console activo, Bing tiene un botón <strong>"Importar de Google"</strong> que hace todo por ti en 1 click.</li>
                            <li>Si lo haces manual: <strong>"Add a site"</strong> → tu dominio → método <strong>"Meta tag"</strong>:<br>
                                <code class="block bg-cream-2 px-3 py-2 rounded-xl mt-1 text-xs break-all">&lt;meta name="msvalidate.01" content="<span class="text-sun-700 font-bold">ABCD1234…</span>"&gt;</code>
                            </li>
                            <li>Pega <strong>solo el valor</strong> aquí abajo, guarda y verifica en Bing.</li>
                        </ol>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1.5">Código de verificación</label>
                            <input type="text" name="bing_site_verification" value="{{ old('bing_site_verification', $setting->bing_site_verification) }}"
                                   placeholder="Solo el valor del content"
                                   class="w-full px-4 py-3 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-sm font-mono">
                            @error('bing_site_verification')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </details>

                {{-- GOOGLE ANALYTICS 4 --}}
                <details class="rounded-3xl border-2 border-ink-200 bg-cream-2/40 overflow-hidden group">
                    <summary class="cursor-pointer px-5 py-4 flex items-center gap-3 hover:bg-cream-2 transition">
                        <span class="grid place-items-center w-9 h-9 rounded-2xl bg-white border border-ink-200 shrink-0">
                            <i class="fa-solid fa-chart-line text-coral-600"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-display font-bold text-ink-900 text-sm">Google Analytics 4</p>
                            <p class="text-xs text-ink-500">Mide tráfico, conversiones y de qué buscador llegan tus lectores.</p>
                        </div>
                        @if (!empty($setting->google_analytics_id))
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-coral-600 text-white px-2 py-0.5 rounded-full">Activo</span>
                        @endif
                        <i class="fa-solid fa-chevron-down text-ink-400 text-xs group-open:rotate-180 transition"></i>
                    </summary>
                    <div class="px-5 pb-5 pt-3 border-t border-ink-200 space-y-4">
                        <ol class="text-sm text-ink-700 space-y-2 list-decimal list-inside leading-relaxed">
                            <li>Crea una propiedad en <a href="https://analytics.google.com" target="_blank" class="text-coral-700 font-semibold hover:underline">analytics.google.com</a> → tipo <strong>"Web"</strong>.</li>
                            <li>Copia el <strong>ID de medición</strong> (formato <code class="bg-cream-2 px-1.5 py-0.5 rounded">G-XXXXXXXXXX</code>).</li>
                            <li>Pégalo abajo. El tag solo se carga en producción, no en desarrollo (no contamina las métricas).</li>
                        </ol>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1.5">ID de medición GA4</label>
                            <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $setting->google_analytics_id) }}"
                                   placeholder="G-XXXXXXXXXX"
                                   class="w-full px-4 py-3 rounded-2xl bg-white border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 text-sm font-mono">
                            @error('google_analytics_id')<p class="text-xs text-coral-500 mt-1.5">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </details>

                {{-- Sitemap rápido --}}
                <div class="rounded-2xl bg-gradient-to-br from-brand-50 to-white border border-brand-200 p-4 flex items-center gap-3">
                    <i class="fa-solid fa-map-location-dot text-brand-700 text-xl"></i>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-ink-900">Tu sitemap está aquí:</p>
                        <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-xs text-brand-700 font-mono hover:underline break-all">{{ url('/sitemap.xml') }}</a>
                    </div>
                    <a href="{{ url('/sitemap.xml') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-brand-300 text-brand-700 text-xs font-bold hover:bg-brand-50 transition shrink-0">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Abrir
                    </a>
                </div>
            </div>
        </section>

        {{-- ─────────── Colores (con preview en vivo) ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-1">
                <i class="fa-solid fa-droplet text-brand-600"></i> Colores
            </h2>
            <p class="text-sm text-ink-500 mb-5">Los cambios se previsualizan en vivo abajo.</p>

            <input type="hidden" name="theme_preset" :value="preset">

            <div class="grid sm:grid-cols-4 gap-4">
                @foreach ([
                    ['brand',  'brand_color',  'Principal'],
                    ['accent', 'accent_color', 'Acento'],
                    ['sun',    'sun_color',    'Sol / amarillo'],
                    ['ink',    'ink_color',    'Texto'],
                ] as [$var, $field, $label])
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="{{ $var }}" @input="preset = 'custom'"
                                   class="w-12 h-12 rounded-2xl border border-ink-200 cursor-pointer">
                            <input type="text" name="{{ $field }}" x-model="{{ $var }}" @input="preset = 'custom'"
                                   class="flex-1 px-3 py-2 rounded-2xl bg-cream-2 border border-ink-200 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-brand-400">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Preview --}}
            <div class="mt-7 rounded-3xl border-2 border-dashed border-ink-200 p-6" :style="`background:${ink}07`">
                <p class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-3">Vista previa en vivo</p>
                <div class="rounded-2xl p-5 shadow-soft border border-white/20" :style="`background:linear-gradient(135deg, ${brand}, ${shade(brand,-20)});`">
                    <h3 class="text-white font-display font-extrabold text-xl">Aprende algo nuevo, a tu manera.</h3>
                    <div class="flex flex-wrap items-center gap-2 mt-4">
                        <button type="button" :style="`background:#fff;color:${ink}`" class="px-4 py-2 rounded-full font-bold text-sm shadow-soft">Explorar cursos</button>
                        <button type="button" :style="`background:${accent};color:#fff`" class="px-4 py-2 rounded-full font-bold text-sm">Crear cuenta</button>
                        <span :style="`background:${sun};color:${ink}`" class="px-3 py-1 rounded-full text-xs font-bold">Nuevo</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────── Tipografía ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-5">
                <i class="fa-solid fa-font text-coral-500"></i> Tipografía
            </h2>

            <div class="grid sm:grid-cols-2 gap-4">
                @php
                    $allFonts = ['Poppins','Inter','Manrope','Plus Jakarta Sans','Outfit','IBM Plex Sans','Space Grotesk','Lato','Roboto'];
                @endphp
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="font_display">Tipografía de títulos (display)</label>
                    <select id="font_display" name="font_display"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                        @foreach ($allFonts as $f)
                            <option value="{{ $f }}" @selected($setting->font_display === $f)>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1.5" for="font_body">Tipografía del cuerpo</label>
                    <select id="font_body" name="font_body"
                        class="w-full px-4 py-3 rounded-2xl bg-cream-2 border border-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:border-brand-400 focus:bg-white transition">
                        @foreach ($allFonts as $f)
                            <option value="{{ $f }}" @selected($setting->font_body === $f)>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p class="text-xs text-ink-400 mt-3">
                <i class="fa-solid fa-circle-info"></i>
                Las fuentes Poppins e Inter ya vienen precompiladas. Para usar otras tendrás que actualizar <code>vite.config.js</code>.
            </p>
        </section>

        {{-- ─────────── Toggles de secciones del home ─────────── --}}
        <section class="bg-white border border-ink-200/70 rounded-3xl shadow-soft p-6 sm:p-8">
            <h2 class="font-display font-extrabold text-lg text-ink-900 flex items-center gap-2 mb-1">
                <i class="fa-solid fa-toggle-on text-brand-600"></i> Secciones del home
            </h2>
            <p class="text-sm text-ink-500 mb-5">Activa o desactiva qué secciones muestra la página de inicio.</p>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @php $enabled = $setting->enabled_sections ?? array_keys($sections); @endphp
                @foreach ($sections as $key => $label)
                    <label class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-cream-2 hover:bg-brand-50 border border-ink-200 transition cursor-pointer">
                        <input type="checkbox" name="enabled_sections[]" value="{{ $key }}"
                               @checked(in_array($key, $enabled))
                               class="w-4 h-4 rounded-md border-ink-300 text-brand-600 focus:ring-brand-400">
                        <span class="text-sm font-medium text-ink-900">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </section>

        {{-- Submit --}}
        <div class="flex flex-wrap justify-end gap-3 sticky bottom-4 z-10">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl font-bold bg-brand-600 text-white hover:bg-brand-700 shadow-lift transition">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function appearance(initial) {
    return {
        ...initial,
        // Aclarar/oscurecer un hex en un % (-100 a 100)
        shade(hex, percent) {
            const f = parseInt(hex.slice(1), 16);
            const t = percent < 0 ? 0 : 255;
            const p = Math.abs(percent) / 100;
            const R = f >> 16, G = (f >> 8) & 0x00FF, B = f & 0x0000FF;
            const r = Math.round((t - R) * p) + R;
            const g = Math.round((t - G) * p) + G;
            const b = Math.round((t - B) * p) + B;
            return '#' + (0x1000000 + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
    };
}
</script>

@endsection
