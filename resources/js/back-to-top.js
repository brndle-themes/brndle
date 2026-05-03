/**
 * Back-to-top floating button.
 *
 * Reveals after the user has scrolled past 400px, smooth-scrolls to the
 * top on click, and respects `prefers-reduced-motion` (instant jump
 * instead of smooth animation).
 *
 * Markup is rendered server-side from
 * `resources/views/partials/components/back-to-top.blade.php`. This
 * controller is tiny on purpose — it should be cheap on every page.
 */

const SHOW_THRESHOLD = 400;
const VISIBLE_CLASS = 'is-visible';

const button = document.querySelector('[data-brndle-back-to-top]');

if (button) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    const update = () => {
        const shouldShow = window.scrollY > SHOW_THRESHOLD;
        button.classList.toggle(VISIBLE_CLASS, shouldShow);
    };

    button.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            left: 0,
            behavior: reduceMotion.matches ? 'auto' : 'smooth',
        });
        // Move focus back to the top of the page so keyboard users don't
        // get stranded at the bottom after the scroll resolves.
        const skipLink = document.querySelector('a[href="#main"]') || document.getElementById('main');
        if (skipLink) {
            skipLink.focus({ preventScroll: true });
        }
    });

    window.addEventListener('scroll', update, { passive: true });
    update();
}
