import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// Scroll-reveal: cuando un elemento .sr entra en pantalla, añade .in
document.addEventListener('DOMContentLoaded', () => {
    const els = document.querySelectorAll('.sr');
    if (!('IntersectionObserver' in window) || els.length === 0) {
        els.forEach((el) => el.classList.add('in'));
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
});
