import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// ─── Scroll-reveal ──────────────────────────────────────────────────────────
// Cuando un elemento .sr entra en pantalla, se le añade .in para revelarlo.
// IMPORTANTE: se registra ANTES de arrancar Alpine y de forma independiente,
// para que aunque Alpine fallara, el contenido (.sr) NUNCA se quede invisible
// para siempre (antes, un error de Alpine cortaba el script y dejaba la página
// en blanco).
const revealAll = (els) => els.forEach((el) => el.classList.add('in'));

const initScrollReveal = () => {
    window.__srReady = true; // avisa al fallback en línea de que el revelado ya corre
    const els = document.querySelectorAll('.sr');
    if (els.length === 0) return;

    if (!('IntersectionObserver' in window)) {
        revealAll(els);
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target);
            }
        });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.08 });

    els.forEach((el) => io.observe(el));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollReveal);
} else {
    initScrollReveal();
}

// Red de seguridad: si por lo que sea el contenido sigue oculto pasado 1s
// (p. ej. un fallo de JS posterior), lo revelamos igualmente.
setTimeout(() => {
    document.querySelectorAll('.sr:not(.in)').forEach((el) => {
        const r = el.getBoundingClientRect();
        if (r.top < window.innerHeight) el.classList.add('in');
    });
}, 1000);

// ─── Alpine.js ──────────────────────────────────────────────────────────────
Alpine.plugin(collapse);
window.Alpine = Alpine;
try {
    Alpine.start();
} catch (e) {
    console.error('Alpine no pudo iniciarse:', e);
}
