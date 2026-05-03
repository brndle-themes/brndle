/**
 * brndle/timeline — frontend reveal animation.
 *
 * Marks items as revealed as they enter the viewport. CSS owns the
 * actual transition; the controller only flips classes. Stagger lives in
 * `transitionDelay` set inline so the browser handles the timing.
 */

const STAGGER_MS = 60;

function init() {
    const lists = Array.from(document.querySelectorAll('.brndle-timeline'));
    if (!lists.length) return;

    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;

    if (reduceMotion || typeof IntersectionObserver === 'undefined') {
        // No reveal animation — show everything immediately.
        lists.forEach((list) => {
            list.querySelectorAll('.brndle-timeline__item').forEach((item) => {
                item.classList.add('is-revealed');
            });
        });
        return;
    }

    lists.forEach((list) => {
        list.dataset.revealReady = '';
        const items = Array.from(list.querySelectorAll('.brndle-timeline__item'));

        const reveal = (item) => {
            const idx = items.indexOf(item);
            item.style.transitionDelay = idx >= 0 ? `${idx * STAGGER_MS}ms` : '0ms';
            item.classList.add('is-revealed');
        };

        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    reveal(entry.target);
                    io.unobserve(entry.target);
                });
            },
            { rootMargin: '0px 0px 100px 0px', threshold: 0 }
        );

        items.forEach((item) => io.observe(item));

        // Safety net: if IO hasn't fired within 1.5s (e.g. an item was
        // observed below the fold but the user never scrolls), reveal
        // unconditionally so content is never permanently invisible.
        setTimeout(() => {
            items.forEach((item) => {
                if (!item.classList.contains('is-revealed')) {
                    reveal(item);
                    io.unobserve(item);
                }
            });
        }, 1500);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
