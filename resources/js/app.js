import './bootstrap';

const headerPill = document.getElementById('site-header-pill');
const mobileToggle = document.getElementById('mobile-menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');

const syncHeaderState = () => {
    if (!headerPill) return;
    headerPill.classList.toggle('is-scrolled', window.scrollY > 24);
};

syncHeaderState();
window.addEventListener('scroll', syncHeaderState, { passive: true });

mobileToggle?.addEventListener('click', () => {
    const isOpen = !mobileMenu?.classList.contains('hidden');
    mobileMenu?.classList.toggle('hidden', isOpen);
    mobileToggle.setAttribute('aria-expanded', String(!isOpen));
});
