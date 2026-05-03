/**
 * Mega menu controller (M1.C)
 *
 * Vanilla JS, idempotent. Discovers every nav item with a submenu
 * (`[data-brndle-has-submenu]`) and gives it:
 *
 *   • Hover-open with 100ms debounce (desktop only — `(hover: hover)`)
 *   • Click-open parity (works on touch + keyboard)
 *   • ARIA flip — `aria-expanded` on the parent <a>
 *   • Click-outside → close
 *   • Escape key → close + return focus to trigger
 *   • Tab through items in document order
 *   • Arrow keys: ←/→ between top-level, ↓ to enter submenu, ↑ leave, Esc close
 *
 * Reads from `[data-brndle-has-submenu]` attributes set by
 * Brndle\Navigation\MegaMenuWalker. The CSS shows / hides the submenu
 * based on `aria-expanded="true"` adjacency selector — JS only flips the
 * attribute, no class manipulation.
 *
 * Mobile drawer / collapse panels are out of scope here — M1.D handles
 * the mobile accordion via `[data-brndle-disclosure]` (different attr,
 * different controller path).
 *
 * @see plans/2026-05-03-mega-menu.md
 */

(function () {
  if (window.brndleMegaMenuInit) return;
  window.brndleMegaMenuInit = true;

  const HOVER_OPEN_DELAY = 100; // ms — debounce flicker on fast cursor moves
  const HOVER_CLOSE_DELAY = 200; // ms — give user time to move into the panel

  const supportsHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  /**
   * Single open submenu at a time, tracked globally so opening B closes A.
   * @type {HTMLElement|null}
   */
  let openTrigger = null;
  let hoverTimer = null;

  function trigger(item) {
    return item.querySelector(':scope > a');
  }

  function openMenu(item) {
    const t = trigger(item);
    if (!t) return;
    if (openTrigger && openTrigger !== t) {
      closeMenu(openTrigger.closest('[data-brndle-has-submenu]'));
    }
    // Mega panels are `position: fixed` for viewport-centered layout
    // (matches what real mega menu plugins do — Stripe, Linear, Shopify).
    // The CSS reads `--brndle-mega-top` for vertical positioning; we set
    // it to the trigger's bottom position on every open so it tracks
    // sticky / banner / glass headers and any future scroll-shrink behavior.
    const mega = item.querySelector(':scope > .brndle-mega');
    if (mega) {
      const rect = t.getBoundingClientRect();
      document.documentElement.style.setProperty(
        '--brndle-mega-top',
        Math.round(rect.bottom + 8) + 'px'
      );
    }
    t.setAttribute('aria-expanded', 'true');
    openTrigger = t;
  }

  function closeMenu(item) {
    if (!item) return;
    const t = trigger(item);
    if (!t) return;
    t.setAttribute('aria-expanded', 'false');
    if (openTrigger === t) openTrigger = null;
  }

  function closeAll(scope) {
    (scope || document).querySelectorAll('[data-brndle-has-submenu] > a[aria-expanded="true"]').forEach(function (a) {
      a.setAttribute('aria-expanded', 'false');
    });
    openTrigger = null;
  }

  function attach(item) {
    if (item.dataset.brndleMegaMenuBound === '1') return;
    item.dataset.brndleMegaMenuBound = '1';

    const t = trigger(item);
    if (!t) return;

    // ARIA defaults — walker already sets these but be defensive.
    if (!t.hasAttribute('aria-haspopup')) t.setAttribute('aria-haspopup', 'true');
    if (!t.hasAttribute('aria-expanded')) t.setAttribute('aria-expanded', 'false');

    // Hover (desktop only).
    if (supportsHover) {
      item.addEventListener('mouseenter', function () {
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () { openMenu(item); }, HOVER_OPEN_DELAY);
      });
      item.addEventListener('mouseleave', function () {
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () { closeMenu(item); }, HOVER_CLOSE_DELAY);
      });
    }

    // Click — works on touch, mouse, and keyboard activation. Prevents the
    // <a>'s own navigation when the trigger has children, on FIRST tap. A
    // second tap (with menu open) navigates. Mirrors common mobile UX.
    t.addEventListener('click', function (e) {
      // Only intercept when the menu is closed AND the link target isn't
      // explicitly something the user clearly wanted (e.g. "#" or empty).
      // If aria-expanded is already true, let the click navigate normally.
      const expanded = t.getAttribute('aria-expanded') === 'true';
      if (expanded) return; // second click → follow link

      // Don't intercept when modifier keys are held (open in new tab etc.)
      if (e.metaKey || e.ctrlKey || e.shiftKey) return;

      e.preventDefault();
      openMenu(item);
    });

    // Keyboard handlers on the trigger.
    t.addEventListener('keydown', function (e) {
      switch (e.key) {
        case 'ArrowDown':
          e.preventDefault();
          openMenu(item);
          // Move focus into first submenu item.
          var firstChild = item.querySelector(':scope > ul.sub-menu > li > a, :scope > .brndle-mega a');
          if (firstChild) firstChild.focus();
          break;
        case 'Escape':
          closeMenu(item);
          break;
      }
    });
  }

  // Global keyboard + click-outside.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && openTrigger) {
      const item = openTrigger.closest('[data-brndle-has-submenu]');
      closeMenu(item);
      openTrigger = null;
      // Return focus to the trigger that was open.
      if (item) trigger(item)?.focus();
    }

    // Arrow navigation within an open submenu.
    if (openTrigger && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
      const item = openTrigger.closest('[data-brndle-has-submenu]');
      const focusables = item.querySelectorAll(':scope > ul.sub-menu a, :scope > .brndle-mega a');
      if (!focusables.length) return;
      const arr = Array.from(focusables);
      const current = arr.indexOf(document.activeElement);
      if (current === -1) return;
      e.preventDefault();
      const next = e.key === 'ArrowDown' ? (current + 1) % arr.length : (current - 1 + arr.length) % arr.length;
      arr[next].focus();
    }
  });

  document.addEventListener('click', function (e) {
    if (!openTrigger) return;
    const item = openTrigger.closest('[data-brndle-has-submenu]');
    if (item && !item.contains(e.target)) {
      closeAll();
    }
  });

  /**
   * Mobile disclosure button handler. The walker emits a
   * `<button data-brndle-disclosure>` next to each parent `<a>` in
   * mobile context. Click toggles the adjacent `<ul.sub-menu>[hidden]`
   * by removing/adding the hidden attribute and rotating the chevron
   * icon (CSS handles the rotation via `aria-expanded`).
   */
  function attachDisclosure(btn) {
    if (btn.dataset.brndleDisclosureBound === '1') return;
    btn.dataset.brndleDisclosureBound = '1';

    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const parentLi = btn.closest('li');
      if (!parentLi) return;
      const submenu = parentLi.querySelector(':scope > ul.sub-menu');
      if (!submenu) return;

      const isOpen = btn.getAttribute('aria-expanded') === 'true';
      if (isOpen) {
        // Closing: animate max-height back to 0 then re-add `hidden`.
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        // Force reflow so the next assignment animates.
        void submenu.offsetHeight;
        submenu.style.maxHeight = '0px';
        btn.setAttribute('aria-expanded', 'false');
        // Restore [hidden] after the transition completes (240ms in CSS).
        setTimeout(function () {
          if (btn.getAttribute('aria-expanded') === 'false') {
            submenu.setAttribute('hidden', '');
            submenu.style.maxHeight = '';
          }
        }, 260);
      } else {
        // Opening: remove [hidden] first so scrollHeight is measurable,
        // then animate max-height from 0 to the natural height.
        submenu.removeAttribute('hidden');
        submenu.style.maxHeight = '0px';
        // Force reflow.
        void submenu.offsetHeight;
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        btn.setAttribute('aria-expanded', 'true');
        // After the animation, clear inline max-height so future content
        // (e.g. content that grows / shrinks) doesn't get clipped.
        setTimeout(function () {
          if (btn.getAttribute('aria-expanded') === 'true') {
            submenu.style.maxHeight = '';
          }
        }, 260);
      }
    });
  }

  // First pass.
  function init() {
    document.querySelectorAll('[data-brndle-has-submenu]').forEach(attach);
    document.querySelectorAll('[data-brndle-disclosure]').forEach(attachDisclosure);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Re-bind after view-transitions soft-nav so a navigation back to a page
  // with a different menu re-attaches handlers without reload.
  document.addEventListener('brndle:soft-nav', init);
})();
