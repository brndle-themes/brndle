/**
 * Header behaviors (M4)
 *
 * Two responsibilities:
 *   1. Sticky modes — `static` / `sticky-fixed` / `sticky-fade` /
 *      `sticky-hide-on-scroll`. Reads `data-brndle-sticky-mode` on the
 *      <body> or <html> root and applies the matching scroll behavior to
 *      `#brndle-header` via class flips. CSS does the actual styling.
 *   2. Search popover — a small toggle that reveals a fixed-position
 *      search panel below the header when the search icon is clicked.
 *      Esc + click-outside close. Focus moves into the search input on
 *      open and back to the trigger on close.
 *
 * All functionality is opt-in via settings; this script early-exits if
 * neither feature is configured. Idempotent — guarded by an init flag.
 */

(function () {
  if (window.brndleHeaderBehaviorsInit) return;
  window.brndleHeaderBehaviorsInit = true;

  const SCROLL_DOWN_THRESHOLD = 80; // pixels from top before sticky modes engage
  const HIDE_ON_SCROLL_DELTA = 8;   // minimum scroll delta (px) before hide / reveal
  const FADE_THRESHOLD = 40;        // px scrolled before sticky-fade applies the scrolled class

  function initSticky() {
    const root = document.documentElement;
    const mode = root.getAttribute('data-brndle-sticky-mode') || 'static';
    if (mode === 'static') return;

    const header = document.getElementById('brndle-header');
    if (!header) return;

    let lastY = window.scrollY;
    let ticking = false;

    function update() {
      ticking = false;
      const y = window.scrollY;
      const delta = y - lastY;

      // `is-scrolled` flips after the user has scrolled past the threshold —
      // useful for sticky-fade modes that want a visual nudge on first scroll.
      header.classList.toggle('is-scrolled', y > FADE_THRESHOLD);

      if (mode === 'sticky-hide-on-scroll' && y > SCROLL_DOWN_THRESHOLD) {
        // Tolerance threshold prevents jitter when delta hovers near 0.
        if (Math.abs(delta) > HIDE_ON_SCROLL_DELTA) {
          header.classList.toggle('is-hidden', delta > 0);
        }
      } else {
        header.classList.remove('is-hidden');
      }

      lastY = y;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) {
        requestAnimationFrame(update);
        ticking = true;
      }
    }, { passive: true });

    // Initial measurement so refresh-while-scrolled doesn't show the wrong state.
    update();
  }

  function initSearch() {
    const trigger = document.querySelector('[data-brndle-search-trigger]');
    const panel = document.getElementById('brndle-search-popover');
    if (!trigger || !panel) return;

    function open() {
      panel.removeAttribute('hidden');
      trigger.setAttribute('aria-expanded', 'true');
      // Focus the input on open for keyboard users.
      const input = panel.querySelector('input[type="search"], input[type="text"]');
      if (input) input.focus();
    }

    function close() {
      panel.setAttribute('hidden', '');
      trigger.setAttribute('aria-expanded', 'false');
      trigger.focus();
    }

    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      const isOpen = !panel.hasAttribute('hidden');
      if (isOpen) close();
      else open();
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !panel.hasAttribute('hidden')) {
        close();
      }
    });

    document.addEventListener('click', function (e) {
      if (panel.hasAttribute('hidden')) return;
      if (!panel.contains(e.target) && !trigger.contains(e.target)) {
        close();
      }
    });
  }

  function init() {
    initSticky();
    initSearch();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
