{{-- ════════════════════════════════════════════════════════════════════
     Cookie banner · cumple GDPR/RGPD básico (UE) y LSSI (España).
     Persistencia en localStorage para no reaparecer al recargar.

     UX:
       - Aparece deslizándose desde abajo a los 800ms (no compite con LCP).
       - 3 opciones: Aceptar / Rechazar / Personalizar.
       - Si el usuario rechaza, NO se cargan scripts de tracking opcionales
         (Google Analytics solo se inyecta tras "Aceptar").

     Si el visitante no es UE y no quieres el banner, edita el guard de Alpine
     para condicionarlo a la región (requiere middleware geoip — fuera de scope).
     ════════════════════════════════════════════════════════════════════ --}}
<div x-data="cookieConsent()"
     x-init="init()"
     x-show="visible"
     x-cloak
     x-transition:enter="transition transform ease-out duration-500"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition transform ease-in duration-300"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0"
     class="fixed bottom-0 left-0 right-0 z-50 px-4 pb-4 sm:pb-6 pointer-events-none"
     role="dialog"
     aria-labelledby="cookie-banner-title"
     aria-describedby="cookie-banner-desc">
    <div class="max-w-5xl mx-auto bg-white border border-ink-200/70 rounded-3xl shadow-lift p-5 sm:p-6 pointer-events-auto">

        {{-- Vista resumen --}}
        <div x-show="!showDetails" class="flex flex-col sm:flex-row items-start gap-4">
            <span class="grid place-items-center w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 shrink-0">
                <i class="fa-solid fa-cookie-bite text-xl"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p id="cookie-banner-title" class="font-display font-bold text-ink-900 text-sm sm:text-base">Usamos cookies para mejorar tu experiencia</p>
                <p id="cookie-banner-desc" class="text-xs sm:text-sm text-ink-600 mt-1 leading-relaxed">
                    Las esenciales son siempre necesarias para que el sitio funcione (sesión, idioma).
                    Las opcionales nos ayudan a entender qué contenido te interesa.
                    Puedes cambiar tu elección en cualquier momento desde el pie de página.
                    <a href="{{ url('/legal/privacy') }}" class="text-brand-700 underline hover:text-brand-600">Política de privacidad</a>.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto shrink-0">
                <button type="button" @click="showDetails = true"
                        class="px-4 py-2.5 rounded-full bg-cream-2 hover:bg-ink-100 text-ink-700 text-xs sm:text-sm font-semibold transition">
                    Personalizar
                </button>
                <button type="button" @click="reject()"
                        class="px-4 py-2.5 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 text-xs sm:text-sm font-semibold transition">
                    Rechazar
                </button>
                <button type="button" @click="acceptAll()"
                        class="px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold transition shadow-soft">
                    Aceptar todo
                </button>
            </div>
        </div>

        {{-- Vista detalle / personalización --}}
        <div x-show="showDetails" x-cloak class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <p class="font-display font-bold text-ink-900">Personalizar cookies</p>
                <button type="button" @click="showDetails = false" class="text-ink-400 hover:text-ink-700" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <label class="flex items-start gap-3 p-3 rounded-2xl bg-cream-2 cursor-not-allowed opacity-70">
                <input type="checkbox" checked disabled class="mt-1 w-4 h-4 rounded text-brand-600">
                <span class="flex-1 text-sm">
                    <span class="font-semibold text-ink-900 block">Esenciales <span class="text-[10px] text-ink-500 font-normal">(siempre activas)</span></span>
                    <span class="text-ink-600 text-xs">Sesión de usuario, carrito, idioma, CSRF.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 p-3 rounded-2xl bg-cream-2 cursor-pointer hover:bg-brand-50 transition">
                <input type="checkbox" x-model="prefs.analytics" class="mt-1 w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="flex-1 text-sm">
                    <span class="font-semibold text-ink-900 block">Analítica</span>
                    <span class="text-ink-600 text-xs">Google Analytics, métricas anónimas de uso. Nos ayuda a mejorar.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 p-3 rounded-2xl bg-cream-2 cursor-pointer hover:bg-brand-50 transition">
                <input type="checkbox" x-model="prefs.marketing" class="mt-1 w-4 h-4 rounded text-brand-600 focus:ring-brand-400">
                <span class="flex-1 text-sm">
                    <span class="font-semibold text-ink-900 block">Marketing</span>
                    <span class="text-ink-600 text-xs">Píxeles de redes para mostrarte contenido relacionado fuera del sitio.</span>
                </span>
            </label>

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <button type="button" @click="reject()" class="flex-1 px-4 py-2.5 rounded-full bg-ink-100 hover:bg-ink-200 text-ink-700 text-sm font-semibold transition">
                    Rechazar opcionales
                </button>
                <button type="button" @click="saveCustom()" class="flex-1 px-4 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold transition shadow-soft">
                    Guardar mi elección
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function cookieConsent() {
    return {
        visible: false,
        showDetails: false,
        prefs: { analytics: false, marketing: false },
        KEY: 'cursalia.cookie.consent.v1',

        init() {
            // Si ya tomó una decisión, no mostrar.
            const saved = localStorage.getItem(this.KEY);
            if (saved) {
                this.prefs = JSON.parse(saved);
                this.applyConsent();
                return;
            }
            // Si no, aparecer a los 800ms para no competir con LCP.
            setTimeout(() => { this.visible = true; }, 800);
        },

        acceptAll() {
            this.prefs = { analytics: true, marketing: true };
            this.persist();
        },
        reject() {
            this.prefs = { analytics: false, marketing: false };
            this.persist();
        },
        saveCustom() {
            this.persist();
        },
        persist() {
            localStorage.setItem(this.KEY, JSON.stringify(this.prefs));
            this.visible = false;
            this.applyConsent();
        },
        applyConsent() {
            // Si acepta analítica y hay GA configurado, cargarlo dinámicamente.
            // (El layout solo inyecta GA si APP_ENV=production además).
            window.dispatchEvent(new CustomEvent('cookie-consent-changed', {
                detail: this.prefs,
            }));
        },
    };
}
</script>
