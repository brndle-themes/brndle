/**
 * brndle/code — frontend controller.
 *
 * Two responsibilities:
 *   1. Lazy-load highlight.js the first time a `.brndle-code` enters
 *      (or approaches) the viewport. Highlighter never loads on pages
 *      without code blocks; never loads twice.
 *   2. Wire the copy-to-clipboard button.
 *
 * Line numbering is rendered server-side in the Blade template, so this
 * controller never has to touch innerHTML — keeping the surface clean
 * of XSS-shaped patterns even though highlight.js itself escapes text.
 */

const HLJS_DEFAULT_CDN = 'https://esm.sh/highlight.js@11.10.0';
const COPY_RESET_MS = 1600;

let hljsPromise = null;

function loadHljs(cdn) {
    if (hljsPromise) {
        return hljsPromise;
    }
    hljsPromise = import(/* webpackIgnore: true */ cdn)
        .then((mod) => mod.default || mod)
        .catch((err) => {
            // eslint-disable-next-line no-console
            console.warn('[brndle/code] Failed to load highlight.js from', cdn, err);
            hljsPromise = null;
            return null;
        });
    return hljsPromise;
}

async function highlightBlock(wrapper, hljsCdn) {
    const codeEl = wrapper.querySelector('.brndle-code__code');
    if (!codeEl || codeEl.dataset.brndleHighlighted === '1') {
        return;
    }

    const language = wrapper.dataset.language || 'plain';
    const isPlain = language === 'plain' || !language;

    if (!isPlain) {
        const hljs = await loadHljs(hljsCdn);
        if (hljs && typeof hljs.highlightElement === 'function') {
            try {
                hljs.highlightElement(codeEl);
            } catch (err) {
                // eslint-disable-next-line no-console
                console.warn('[brndle/code] highlightElement failed', err);
            }
        }
    }

    codeEl.dataset.brndleHighlighted = '1';
}

function wireCopyButton(wrapper) {
    const btn = wrapper.querySelector('[data-brndle-code-copy]');
    if (!btn || btn.dataset.brndleCopyWired === '1') {
        return;
    }
    btn.dataset.brndleCopyWired = '1';

    btn.addEventListener('click', async () => {
        const codeEl = wrapper.querySelector('.brndle-code__code');
        if (!codeEl) return;

        // textContent strips any tokenized HTML highlight.js added,
        // giving the user the raw source they expect on paste.
        const text = codeEl.textContent || '';

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
            } else {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
            }
        } catch (err) {
            // eslint-disable-next-line no-console
            console.warn('[brndle/code] Clipboard write failed', err);
            return;
        }

        btn.setAttribute('aria-pressed', 'true');
        const success = btn.querySelector('.brndle-code__copy-success');
        if (success) success.removeAttribute('hidden');

        clearTimeout(btn._brndleResetTimer);
        btn._brndleResetTimer = setTimeout(() => {
            btn.removeAttribute('aria-pressed');
            if (success) success.setAttribute('hidden', '');
        }, COPY_RESET_MS);
    });
}

function init() {
    const wrappers = Array.from(document.querySelectorAll('.brndle-code'));
    if (!wrappers.length) {
        return;
    }

    const cdn =
        (window.brndleCode && window.brndleCode.cdn) ||
        wrappers[0].dataset.cdn ||
        HLJS_DEFAULT_CDN;

    wrappers.forEach(wireCopyButton);

    if (typeof IntersectionObserver === 'undefined') {
        wrappers.forEach((w) => highlightBlock(w, cdn));
        return;
    }

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                io.unobserve(entry.target);
                highlightBlock(entry.target, cdn);
            });
        },
        { rootMargin: '200px 0px' }
    );

    wrappers.forEach((w) => io.observe(w));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
