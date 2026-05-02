# Astro-level performance — what the theme can do

_Written 2026-05-02 as a follow-up to the long-term roadmap. Strictly
theme-scope: no server config, no Cloudflare rules, no WP-Rocket setup.
Anything here can ship by editing files inside `wp-content/themes/brndle`._

## What's already in place

- Server-rendered Blade blocks (zero hydration cost)
- No jQuery, no runtime CSS-in-JS, no page builder
- Local woff2 fonts with `<link rel="preload">` for primary, `font-display: swap` everywhere
- `loading="lazy"` + `decoding="async"` on every non-LCP image
- Conditional JS: dark-mode (1.98 KiB) only when toggle enabled, lead-form view script only when block actually renders
- `should_load_separate_core_block_assets => __return_true` keeps core block CSS scoped per block
- Performance opt-outs in admin (remove emoji script, remove embed script, remove global styles on landing template)

The theme is already fast. This plan closes the *feel* gap to Astro by
adding patterns Astro popularised (view transitions, speculation rules,
critical CSS) — not by re-architecting what already works.

## Operating principle

Same as the long-term roadmap: prefer the structural pattern that makes
the next thing free. A perf trick wired into one block isn't a feature;
it's debt. Wire each pattern into a service provider or composer once
and let every block/template benefit.

## Phase 1 — `<head>` polish (no behaviour change)

Each item is one PHP hook in the relevant service provider, no
template touches required.

1. **Speculation Rules** — emit a `<script type="speculationrules">` from
   `wp_head` that prefetches same-origin links on hover/touch. Native
   browser API, zero theme JS, instant-feeling navigation in Chrome /
   Edge. Gracefully ignored elsewhere. Safe defaults: prefetch on
   `moderate` eagerness, exclude `/wp-admin/*` and `/wp-login.php`.

2. **Hero / LCP image preload** — extend `outputFontPreloads()` in
   `SettingsServiceProvider` (or add a sibling) that walks the post's
   blocks and, for the first hero / content-image-split / video-embed
   block on the page, emits `<link rel="preload" as="image"
   fetchpriority="high">`. Same approach Astro uses for its
   `<Image priority>` directive.

3. **`fetchpriority="high"`** on the hero `<img>` itself, set in
   `hero.blade.php`. Works alongside the preload to tell the browser
   this is the LCP candidate.

4. **`<link rel="preconnect">`** for known external origins — Mailchimp
   when `mailchimp_api_key` is set, only on pages that actually render
   a `lead-form` block.

5. **HTTP early hints support** — when Acorn / WP supports it, emit the
   font + LCP image hints as 103 Early Hints headers in addition to the
   `<head>` tags. Lower priority because server support is uneven.

## Phase 2 — Critical CSS

Today every page ships the full ~104 KiB (gzipped 17.86 KiB) `app.css`
as a render-blocking stylesheet. Astro-feel requires the above-the-fold
CSS to be inline.

6. **Build-time critical CSS extraction** — Vite plugin (or a
   `bin/extract-critical.mjs` post-build step) that runs Penthouse /
   Critical against each page template (`layouts/app.blade.php`,
   `layouts/landing.blade.php`) plus each archive layout. Output one
   `critical-{template}.css` per template.

7. **Inline critical, defer the rest** — service provider hook that:
   - Reads the matching `critical-*.css` for the current template
   - Inlines it via `wp_head` priority 1
   - Adds `media="print" onload="this.media='all'"` (or
     `rel="preload" as="style"`) to the main `app.css` `<link>` so it
     loads non-blocking

8. **Per-template CSS code-split** — split `app.css` into:
   - `app-base.css` (typography, design tokens, dark-mode, prose)
   - `app-blocks.css` (only the `.brndle-section-*` rules + utilities
     used by blocks)
   Templates that don't use blocks (single posts, archives) skip the
   block stylesheet entirely. Pairs with the existing
   `should_load_separate_core_block_assets`.

## Phase 3 — Soft navigation + view transitions

Astro 4 shipped `<ClientRouter>` for same-origin nav with view
transitions. The theme can do the same in ~3 KiB of vanilla JS.

9. **View Transitions API opt-in** — emit
   `<meta name="view-transition" content="same-origin">` from the theme
   on opt-in via admin (default off). Makes default nav use the
   browser's view transition crossfade with zero JS.

10. **Soft navigation controller** — small viewScript that
    intercepts same-origin link clicks, fetches the next page's HTML,
    swaps `<main>` and `<title>` while wrapping in
    `document.startViewTransition()`. ~3 KiB. Falls back to default
    nav when:
    - The link has `data-no-soft-nav`
    - User is on a `prefers-reduced-motion` device
    - View Transitions API isn't available
    - The link target is a different host or has `download`

11. **Hover prefetch as JS fallback** — for browsers without
    Speculation Rules (Phase 1), the soft-nav controller already has
    the link hover handler; reuse it to fetch the HTML in advance.

The pair (view transitions + soft nav) is what gives Astro its
distinctive "feels like an app" character. WP themes rarely ship this.

## Phase 4 — Image pipeline

12. **Modern format `<picture>` wrapper** — Blade component
    `<x-img>` that emits `<picture>` with `<source type="image/avif">`
    + `<source type="image/webp">` + `<img>`. Used by every block's
    image render. Pairs with WP's existing `wp_get_attachment_image_src`
    + a small helper that yields the AVIF/WebP variants if the upload
    pipeline produced them (most modern hosts already do).

13. **`fetchpriority` automation** — the same component takes a
    `priority="high"` prop that auto-applies `fetchpriority="high"` +
    omits `loading="lazy"`. Hero / content-image-split first image gets
    it; everything else stays lazy.

14. **`width` / `height` attributes always set** — prevents CLS. Already
    done in some blocks; audit and enforce via the `<x-img>` component.

15. **Dimensions-aware lazy threshold** — use `IntersectionObserver` to
    pre-fetch images 200 px before viewport entry instead of native
    lazy's harder threshold. ~500 bytes JS, big perceived speed win on
    long pages.

## Phase 5 — Bundle hygiene

16. **Aggressive Tailwind purge** — current `@source` globs scan all
    PHP / Blade / JS. Verify nothing pulls in unused utilities. Compare
    bundle size before/after stricter globs.

17. **Tree-shake `@wordpress/icons`** — currently each block imports
    one named icon. Verify webpack / wp-scripts emits only those —
    don't ship the full icon set.

18. **Remove `app-l0sNRNKZ.js`** (the empty 0.00 KB JS) — it's a Vite
    artifact for `resources/js/app.js` which only contains
    `import.meta.glob`. Either delete the file or actually use it for
    something.

19. **Shorter font loading list** — `package.json`'s `fonts:copy` step
    copies 7 variable fonts on every build. Only copy the variants
    actually referenced by the active font pair.

## Phase 6 — Repeat-visit speed

20. **Service worker for stale-while-revalidate** — register a tiny
    worker that caches:
    - Built assets (`/wp-content/themes/brndle/public/build/*`)
    - Visited HTML pages (LRU, 50 entries)
    Repeat visits are instant; offline gracefully shows cached HTML.
    Theme-shippable: register from app.js, ship `service-worker.js` in
    `public/`. Admin opt-in toggle.

21. **Save-Data + reduced-motion + reduced-data variants** — already
    handle `prefers-reduced-motion`; extend to `prefers-reduced-data`
    so users on metered connections get smaller hero images and no
    autoplay video.

## Phase 7 — Observability

22. **Server-Timing emission** — the theme emits a single
    `Server-Timing` header on each request: blade-render-ms,
    settings-load-ms, css-vars-build-ms. Lets us watch perf
    regressions in real-user data without external tooling.

23. **Built-in `?perf=1` query** — when set, theme injects a tiny
    overlay showing LCP / CLS / INP from `PerformanceObserver`. Same
    pattern as Astro's dev overlay. Admin-only.

## Out of scope (server / infra, not theme)

- Cloudflare Cache Rules, APO, Argo, R2
- WP-Rocket / WP-Optimize / LiteSpeed configuration
- HTTP/3, Brotli, TLS 1.3
- DNS, edge geography
- Page caching plugins

The theme should *play well* with each (no cache busters, valid
Cache-Control hints, no inline secrets) but not implement them.

## Suggested order

1. Phase 1 (#1 + #2 + #3 — speculation rules, LCP preload, fetchpriority)
   — biggest perceived win for least code, two short PRs.
2. Measure baseline with Lighthouse + WebPageTest, save numbers in
   `plans/`. Without this, later phases are guesswork.
3. Phase 4 (#12 — `<x-img>` picture wrapper) — once measured, AVIF/WebP
   adoption gives the next big LCP / total-bytes win.
4. Phase 2 (#6–#8 — critical CSS) — needs the baseline first; the
   trade-off is build-time complexity vs render-blocking elimination.
5. Phase 3 (#9–#11 — view transitions + soft nav) — Astro-distinctive
   feature; ship behind admin toggle.
6. Phase 5–6 (bundle hygiene + service worker) — diminishing returns,
   tackle once 1–4 land.
7. Phase 7 (observability) — useful once the theme has many of these
   features, less so today.

Each phase produces one or two PRs. None of them require a major
version bump as long as defaults are conservative (view transitions
opt-in, service worker opt-in, soft nav respects reduced-motion).

## What this gets us

If all seven phases ship, brndle should match or beat Astro defaults
on:

- LCP (preloaded LCP image + critical CSS + fetchpriority)
- CLS (consistent width/height on every image)
- INP (no soft-nav blocking work; view transitions are GPU-accelerated)
- Total JS (still under 5 KiB on a frontend page without the soft-nav
  router; ~8 KiB with it)
- Repeat-visit feel (service worker)

What it can't match without server changes:

- TTFB on origin (WP database round-trips dominate; a page cache plugin
  closes this gap)
- Cold-start LCP from far-from-origin geography (needs CDN)

Both are checkbox items for the deployment, not the theme.
