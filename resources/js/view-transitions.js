/**
 * Brndle soft-navigation + view-transitions controller.
 *
 * Same-origin link clicks intercept the default navigation, fetch the
 * destination HTML, and swap `<main>` + `<title>` (plus the language
 * attribute) inside `document.startViewTransition()` so the browser
 * crossfades. Behaves like a tiny SPA without shipping a router or
 * touching state — back/forward, scroll position, focus and external
 * links all keep their normal browser behaviour.
 *
 * Bails (default navigation runs) when:
 *   - View Transitions API isn't supported
 *   - User has prefers-reduced-motion
 *   - Link has download / target=_blank / rel=external / data-no-soft-nav
 *   - Link goes to a different host
 *   - Modifier key (cmd/ctrl/shift/alt) is held
 *   - Click is on a hash-only link (let the browser scroll)
 *   - Response isn't HTML or returns an error
 *
 * Loaded only when the admin Performance → View Transitions toggle is on
 * (see resources/views/layouts/app.blade.php). Zero footprint when off.
 */

const SAME_ORIGIN = location.origin;
const reducedMotion = () =>
  window.matchMedia &&
  window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const supported = () =>
  typeof document.startViewTransition === 'function';

function shouldIntercept(event, anchor) {
  if (event.defaultPrevented) return false;
  if (event.button !== 0) return false;
  if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
  if (!anchor || !anchor.href) return false;
  if (anchor.target && anchor.target !== '' && anchor.target !== '_self') return false;
  if (anchor.hasAttribute('download')) return false;
  if (anchor.hasAttribute('data-no-soft-nav')) return false;
  if ((anchor.getAttribute('rel') || '').split(/\s+/).includes('external')) return false;

  const url = new URL(anchor.href, location.href);
  if (url.origin !== SAME_ORIGIN) return false;
  // Hash-only navigation — let the browser scroll natively.
  if (
    url.pathname === location.pathname &&
    url.search === location.search &&
    url.hash !== ''
  ) {
    return false;
  }
  return true;
}

async function fetchPage(url) {
  const response = await fetch(url, {
    headers: { Accept: 'text/html,application/xhtml+xml' },
    credentials: 'same-origin',
  });
  if (!response.ok) throw new Error(`HTTP ${response.status}`);
  const contentType = response.headers.get('content-type') || '';
  if (!contentType.includes('text/html')) {
    throw new Error('Not HTML');
  }
  const html = await response.text();
  const doc = new DOMParser().parseFromString(html, 'text/html');
  if (!doc.querySelector('main')) {
    throw new Error('No <main> in response');
  }
  return doc;
}

function applyDoc(nextDoc, url) {
  const currentMain = document.querySelector('main');
  const nextMain = nextDoc.querySelector('main');
  if (!currentMain || !nextMain) return false;

  // Swap content the browser actually sees — main, title, lang, body class.
  document.title = nextDoc.title || document.title;
  const lang = nextDoc.documentElement.getAttribute('lang');
  if (lang) document.documentElement.setAttribute('lang', lang);
  // Replace body class so any per-template body classes update.
  const bodyClass = nextDoc.body.getAttribute('class');
  if (bodyClass !== null) document.body.setAttribute('class', bodyClass);
  currentMain.replaceWith(nextMain);

  // Notify any listeners (block view-scripts, analytics, etc.) that the
  // page swapped — same intent as Astro's `astro:page-load`.
  document.dispatchEvent(
    new CustomEvent('brndle:soft-nav', {
      detail: { url: url || location.href },
    })
  );
  return true;
}

async function navigate(url, { push = true } = {}) {
  let nextDoc;
  try {
    nextDoc = await fetchPage(url);
  } catch (_err) {
    location.assign(url);
    return;
  }

  const swap = () => applyDoc(nextDoc, url);

  if (push) {
    history.pushState({ brndleSoftNav: true }, '', url);
  }

  if (supported() && !reducedMotion()) {
    document.startViewTransition(swap);
  } else {
    swap();
  }

  // Reset scroll for fresh-page navigation; let the browser handle
  // back/forward scroll restoration natively (we don't override on popstate).
  if (push) window.scrollTo({ top: 0 });
}

function onClick(event) {
  const anchor = event.target.closest && event.target.closest('a[href]');
  if (!anchor) return;
  if (!shouldIntercept(event, anchor)) return;
  event.preventDefault();
  navigate(anchor.href);
}

function onPopState(event) {
  // Only handle entries we pushed; let core navigation handle the rest.
  if (!event.state || !event.state.brndleSoftNav) return;
  navigate(location.href, { push: false });
}

function boot() {
  if (window.__brndleSoftNavBooted) return;
  window.__brndleSoftNavBooted = true;
  document.addEventListener('click', onClick);
  window.addEventListener('popstate', onPopState);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
