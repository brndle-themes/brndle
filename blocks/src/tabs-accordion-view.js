/**
 * brndle/tabs-accordion — frontend controller.
 *
 * Tabs:      WAI-ARIA Tabs Pattern (←/→/Home/End to move between tabs;
 *            Tab moves into the active panel).
 * Accordion: WAI-ARIA Disclosure pattern. Single mode auto-closes
 *            siblings on open; multiple mode keeps any number open.
 *
 * Both controllers respect prefers-reduced-motion via CSS — the JS
 * just toggles `[hidden]` and aria-* attributes; CSS owns the timing.
 */

function initTabsBlock(root) {
    const tablist = root.querySelector('[role="tablist"]');
    if (!tablist) {
        return;
    }
    const tabs = Array.from(tablist.querySelectorAll('[role="tab"]'));
    if (!tabs.length) {
        return;
    }
    const panels = tabs
        .map((tab) => document.getElementById(tab.getAttribute('aria-controls')))
        .filter(Boolean);

    function activate(index, focus = true) {
        tabs.forEach((tab, i) => {
            const active = i === index;
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.setAttribute('tabindex', active ? '0' : '-1');
            if (active && focus) {
                tab.focus();
                // Auto-scroll the active tab into view on mobile strips.
                if (typeof tab.scrollIntoView === 'function') {
                    tab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                }
            }
        });
        panels.forEach((panel, i) => {
            if (!panel) return;
            if (i === index) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activate(index, true));
        tab.addEventListener('keydown', (event) => {
            const last = tabs.length - 1;
            let next = -1;
            switch (event.key) {
                case 'ArrowRight':
                    next = index === last ? 0 : index + 1;
                    break;
                case 'ArrowLeft':
                    next = index === 0 ? last : index - 1;
                    break;
                case 'Home':
                    next = 0;
                    break;
                case 'End':
                    next = last;
                    break;
                default:
                    return;
            }
            event.preventDefault();
            activate(next, true);
        });
    });
}

function initAccordionBlock(root) {
    const items = Array.from(root.querySelectorAll('.brndle-tabs-accordion__acc-item'));
    if (!items.length) {
        return;
    }
    const mode = root.getAttribute('data-mode') === 'multiple' ? 'multiple' : 'single';

    function setOpen(item, open) {
        const trigger = item.querySelector('.brndle-tabs-accordion__acc-trigger');
        const panel = item.querySelector('.brndle-tabs-accordion__acc-panel');
        if (!trigger || !panel) return;

        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            panel.removeAttribute('hidden');
            item.classList.add('is-open');
            // Animate to natural height, then release the cap so resizes
            // reflow correctly.
            panel.style.maxHeight = '0px';
            // Force reflow so the transition sees the from-state.
            // eslint-disable-next-line no-unused-expressions
            panel.offsetHeight;
            panel.style.maxHeight = panel.scrollHeight + 'px';
            const release = () => {
                panel.style.maxHeight = '';
                panel.removeEventListener('transitionend', release);
            };
            panel.addEventListener('transitionend', release);
        } else {
            // Set explicit max-height so the close transition has a
            // from-state, then collapse to 0.
            panel.style.maxHeight = panel.scrollHeight + 'px';
            // eslint-disable-next-line no-unused-expressions
            panel.offsetHeight;
            panel.style.maxHeight = '0px';
            const finish = () => {
                panel.setAttribute('hidden', '');
                panel.style.maxHeight = '';
                item.classList.remove('is-open');
                panel.removeEventListener('transitionend', finish);
            };
            panel.addEventListener('transitionend', finish);
        }
    }

    items.forEach((item) => {
        const trigger = item.querySelector('.brndle-tabs-accordion__acc-trigger');
        if (!trigger) return;
        trigger.addEventListener('click', () => {
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';
            if (mode === 'single' && !isOpen) {
                items.forEach((other) => {
                    if (other !== item) setOpen(other, false);
                });
            }
            setOpen(item, !isOpen);
        });
    });
}

function init() {
    document.querySelectorAll('.brndle-tabs-accordion').forEach((root) => {
        if (root.dataset.brndleTaWired === '1') return;
        root.dataset.brndleTaWired = '1';

        if (root.classList.contains('is-mode-tabs')) {
            initTabsBlock(root);
        } else if (root.classList.contains('is-mode-accordion')) {
            initAccordionBlock(root);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
