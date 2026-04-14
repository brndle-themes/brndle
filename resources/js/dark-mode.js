/**
 * Brndle dark-mode controller.
 *
 * Single global handler for every `[data-brndle-dark-toggle]` button on the
 * page. Runs exactly once regardless of how many toggles are rendered (the
 * Blade partial is intentionally dumb — no IDs, no inline script). Cycles
 * through three real states: system → dark → light → system. System mode
 * clears localStorage so `prefers-color-scheme` drives the paint.
 *
 * The boot script in the document <head> has already applied the correct
 * initial `data-theme` attribute before this module runs, so there is no
 * flash-of-wrong-theme.
 */

const STORAGE_KEY = 'brndle-theme';
const ANNOUNCE_ID = 'brndle-theme-announce';
const VALID = ['light', 'dark', 'system'];

const labels = {
  light: 'Light mode active. Click to switch to dark.',
  dark: 'Dark mode active. Click to switch to system.',
  system: 'System mode active. Click to switch to light.',
};

const announcements = {
  light: 'Light mode enabled',
  dark: 'Dark mode enabled',
  system: 'System mode enabled — follows your OS preference',
};

function currentState() {
  const attr = document.documentElement.getAttribute('data-theme');
  return VALID.includes(attr) ? attr : 'system';
}

function nextState(state) {
  if (state === 'system') return 'dark';
  if (state === 'dark') return 'light';
  return 'system';
}

function ensureAnnouncer() {
  let el = document.getElementById(ANNOUNCE_ID);
  if (el) return el;
  el = document.createElement('div');
  el.id = ANNOUNCE_ID;
  el.className = 'sr-only';
  el.setAttribute('aria-live', 'polite');
  el.setAttribute('aria-atomic', 'true');
  document.body.appendChild(el);
  return el;
}

function paintButtons(state) {
  const buttons = document.querySelectorAll('[data-brndle-dark-toggle]');
  buttons.forEach((btn) => {
    btn.setAttribute('aria-label', labels[state]);
    btn.setAttribute('data-brndle-state', state);
    btn.querySelectorAll('[data-brndle-icon]').forEach((icon) => {
      const name = icon.getAttribute('data-brndle-icon');
      icon.classList.toggle('hidden', name !== state);
    });
  });
}

function applyState(state, { announce = true } = {}) {
  if (!VALID.includes(state)) state = 'system';

  document.documentElement.setAttribute('data-theme', state);

  try {
    if (state === 'system') {
      localStorage.removeItem(STORAGE_KEY);
    } else {
      localStorage.setItem(STORAGE_KEY, state);
    }
  } catch (e) {
    /* localStorage may be blocked (private mode, quota) — ignore */
  }

  paintButtons(state);

  if (announce) {
    const el = ensureAnnouncer();
    el.textContent = announcements[state] || '';
  }
}

function onClick(event) {
  const btn = event.currentTarget;
  if (!btn) return;
  applyState(nextState(currentState()));
}

function attach() {
  const buttons = document.querySelectorAll('[data-brndle-dark-toggle]');
  buttons.forEach((btn) => {
    if (btn.__brndleBound) return;
    btn.__brndleBound = true;
    btn.addEventListener('click', onClick);
  });
  paintButtons(currentState());
}

function boot() {
  if (window.__brndleDarkModeBooted) {
    attach();
    return;
  }
  window.__brndleDarkModeBooted = true;

  attach();

  // Keep newly-injected toggles (e.g. from dynamic blocks) in sync.
  if (typeof MutationObserver !== 'undefined') {
    new MutationObserver(() => attach()).observe(document.body, {
      childList: true,
      subtree: true,
    });
  }

  // Respond to OS changes only when the user has chosen system mode.
  if (window.matchMedia) {
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    const handler = () => {
      if (currentState() === 'system') paintButtons('system');
    };
    if (mq.addEventListener) mq.addEventListener('change', handler);
    else if (mq.addListener) mq.addListener(handler);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}
