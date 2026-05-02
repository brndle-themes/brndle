=== Brndle ===
Contributors: brndlethemes
Tags: blog, custom-logo, custom-menu, featured-images, full-width-template, theme-options, translation-ready
Tested up to: 6.8
Stable tag: 1.4.1
Requires at least: 6.6
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enterprise WordPress theme with AI-powered landing pages, Blade templating, and Tailwind CSS. Zero page builders. Lighthouse 100.

== Description ==

Brndle is a free, open-source WordPress theme for agencies. One theme, unlimited client sites — built on Sage / Acorn (Laravel Blade + Tailwind CSS v4 + Vite 7) with 14 server-rendered Gutenberg blocks for landing pages.

* 12 color schemes
* 8 font pairs
* 8 header styles, 6 footer styles
* 5 archive layouts, 8 single post layouts
* 4 page templates (default, landing, canvas, transparent)
* Dark mode (toggle, system, or off)
* 14 custom landing-page blocks, all server-rendered
* AI landing-page generation via Claude Code
* Full-page Lighthouse 100 baseline
* WPML / Polylang / Yoast / RankMath / WooCommerce compatible

== Installation ==

1. Download the latest `brndle-x.x.x.zip` from the GitHub releases page.
2. In WP Admin, go to Appearance → Themes → Add New → Upload Theme.
3. Upload the zip and activate.
4. Configure under Brndle in the admin sidebar.

No build tools required for end users — the release zip ships compiled assets.

== Changelog ==

= 1.4.1 =
* **Hotfix:** the 1.4.0 release zip excluded `resources/css/critical.css` because `bin/release.sh` was stripping the entire `resources/css/` directory. With 1.4.0's `perf_critical_css` default flipped to true, fresh installs that triggered the inline-critical path then dropped `app.css` from `@vite` AND failed to read the missing critical.css — leaving pages with zero stylesheets (attowp.com surfaced this within minutes of the upgrade). Two-prong fix:
    1. `bin/release.sh` now keeps `resources/css/critical.css` in the zip; only the source `app.css` and `editor.css` files (which compile into `public/build/`) are excluded.
    2. Both layout templates (`app.blade.php`, `landing.blade.php`) now degrade gracefully — if `perf_critical_css` is on but `critical.css` can't be read, they fall back to render-blocking `app.css` instead of leaving the page CSS-less.
* If you're already on 1.4.0 and seeing unstyled pages, the immediate workaround is to toggle Critical CSS off from admin Brndle → Performance. Upgrading to 1.4.1 (or any later version) makes the toggle safe to leave on.

= 1.4.0 =
* Improvement: View Transitions and Critical CSS now default to ON for fresh installs and any site that hasn't yet opened the admin Performance tab. Brndle is primarily used for pages and blogs — soft-navigation between articles and faster first paint on landing pages are exactly the wins those sites need. Existing sites that already saved their settings keep their explicit values; the flip only affects new / never-touched installs. Both features remain admin-toggleable and respect `prefers-reduced-motion`.
* Improvement: Block attribute deprecation registry (`Brndle\Blocks\AttributeMigrations`) wired into every render callback. Sets the structural pattern needed to drop legacy attribute branches in Blade templates after future schema changes — same idempotent-function-per-version model as the settings migration registry from 1.3.3.
* Improvement: Settings schema metadata (`Defaults::schema()`) describes section / label / control / range for all 45 settings. Foundation for the future schema-driven admin UI; today the existing tabs ship unchanged. The settings consistency CI now also asserts every key has matching schema metadata.
* New: `bin/check-blade-compile.php` — pure-PHP dry-run that compiles every Blade template through Acorn's BladeCompiler and runs `token_get_all($source, TOKEN_PARSE)` on the output. Catches the comparison-table-bug class (compiler emits malformed PHP). Fixed six latent issues on first run: 5 unescaped CSS at-rules in inline `<style>` blocks (`@keyframes`, `@media` → `@@keyframes`, `@@media`) and the `<html @php(language_attributes())>` Blade-regex collision in 5 layouts (now raw `<?php language_attributes(); ?>`).
* New: `tests/e2e/journey.spec.js` — Playwright integration test (`npm run test:e2e`) covering frontend perf head tags, palette swap, LCP preload, admin settings, REST endpoint. Caught a real frontend regression on first run — `.brndle-section-dark <h1>` was rendering dark in light-mode sessions because Tailwind preflight broke inheritance. Fix shipped: explicit `color: inherit` rule.
* New: GitHub Actions workflow templates (`bin/github-actions/upstream-check.yml`, `release.yml`) ready for one-`cp` install — handles Mondays-09:00-UTC upstream drift tracking and manual workflow_dispatch version bumps.

= 1.3.3 =
* New: `<x-img>` Blade component with AVIF / WebP `<picture>` wrapper. Detects sibling `*.avif` / `*.webp` files next to local uploads and emits matching `<source>` elements; falls back to a plain `<img>` for external URLs or when no variant is on disk. Width / height / fetchpriority / loading flow through one `priority` prop. Adopted across hero, content-image-split, team, testimonials, and logos blocks — sites that already produce modern image variants serve them automatically.
* New: View Transitions API + soft-navigation controller (admin opt-in, default off). When enabled, emits `<meta name="view-transition" content="same-origin">` for native crossfade in Chrome / Edge 126+ and ships a 2.1 KiB vanilla-JS controller that intercepts same-origin clicks, fetches the destination, and swaps `<main>` + `<title>` + `<html lang>` inside `document.startViewTransition()`. Bails for downloads / external / cross-origin / reduced-motion / non-HTML / missing `<main>`. Dispatches `brndle:soft-nav` on document so block view-scripts can rebind.
* New: Critical CSS opt-in (admin Performance → Critical CSS, default off). Inlines a 3.2 KiB hand-curated stylesheet covering reset, body / html base, `.brndle-section-*` variants, hero layout primitives, and skip-link rule; defers the full `app.css` via `rel=preload` + `onload` swap so the first paint isn't render-blocked. `<noscript>` fallback restores blocking behaviour when JS is off.
* New: Lucide as the frontend SVG library (alongside `@wordpress/icons` for editor JSX). Curated `bin/copy-lucide-icons.mjs` writes Lucide SVGs into `resources/icons/` at build time; new `<x-icon name="kebab-case">` Blade component renders them inline with a single attribute. First adoption: FAQ summary plus icon. Convention recorded in CLAUDE.md — never use emoji as a UI affordance.
* Improvement: Settings migration registry — `Brndle\Settings\Migrations::all()` is now the single source of truth for schema upgrades. Each migration is a pure idempotent function applied in version order. Sets the structural pattern needed before any settings rename / restructure ships safely. `Settings::VERSION` stays at 1 today; the registry is ready for v2 the next time a key is restructured.
* Improvement: Settings consistency CI guardrail extended to 45 keys (was 43) — every PR fails fast if `Defaults.php` / admin tabs / code consumers drift apart.

= 1.3.2 =
* New: Speculation Rules — pages now emit a `<script type="speculationrules">` so Chrome and Edge prefetch same-origin links the user is likely to visit next, making navigation feel instant. Excludes `/wp-admin/*`, `/wp-login.php`, and any link with `rel="external"` or `data-no-prefetch`.
* New: LCP image preload — pages with a hero, content+image, or video-embed block emit `<link rel="preload" as="image" fetchpriority="high">` for the first such image, shaving 100–400 ms of LCP on hero-led layouts.
* New: Conditional integration preconnect — when a lead-form block is on the page and Mailchimp is configured, the page warms the DNS / TLS to the correct Mailchimp datacenter via `<link rel="preconnect">` + `dns-prefetch`. Cost is paid only on pages that actually use the integration.
* New: Filter hooks `brndle/perf/speculation_rules`, `brndle/perf/lcp_preload`, `brndle/perf/preconnects`, and `brndle/perf/preconnect_origins` for site-level opt-out / customisation.
* Improvement: Typography → Heading Scale Ratio admin slider now scales `<h1>`–`<h6>` inside `.prose` content (blog posts, page bodies). Block markup keeps its design-intent `text-[clamp(...)]` sizes.
* Improvement: Settings consistency CI guardrail (`bin/check-settings-consistency.mjs`) — every PR now fails fast if a key in `Defaults.php` lacks an admin tab field or a code consumer, or if an admin tab references a key that doesn't exist in defaults. Closes the kind of silent drift that produced the 1.3.0 dead settings.
* Chore: Drop the empty `resources/js/app.js` Vite shim. Phase-5 bundle hygiene from the perf roadmap — every page is now one fewer network request and the build manifest shrinks 1.36 → 0.82 KiB.

= 1.3.1 =
* Fix: Typography → Base Font Size and Heading Scale Ratio sliders now actually scale rendered output. Previously the admin saved + emitted CSS variables but no stylesheet read them. `html { font-size: var(--font-size-base) }` makes the rem cascade scale; `--text-h1` … `--text-h6` are now exposed as a calc() ramp from `--heading-scale` for opt-in heading sizing in custom CSS.
* Fix: Tailwind `dark:` modifier is now bound to brndle's `[data-theme="dark"]` toggle via a `@custom-variant`. Previously `dark:foo` only fired on `prefers-color-scheme: dark` — the toggle worked accidentally for the three `dark:` utilities currently used (`dark:hidden`, `dark:block`, `dark:prose-invert`) because each was manually re-implemented under `[data-theme="dark"]`. New utilities will now just work. The variant also covers `[data-theme="system"]` + OS-dark and the no-attribute fallback so child themes that drop the attribute keep OS-driven dark styling.
* Refactor: Block editor iframe styles now flow through Sage's idiomatic `Vite::asset('editor.css')` injection instead of a parallel enqueue path in BlockServiceProvider. `editor.css` `@import`s `app.css` so the iframe inherits the same Tailwind theme tokens, source globs, and `.brndle-section-*` rules as the frontend (-66 lines, single iframe-loading path).
* Chore: Adds `bin/check-upstream.sh` + a CLAUDE.md upstream-tracking section so framework drift from `roots/sage` is visible at a glance (Acorn 5→6, Vite 7→8, etc.).
* Chore: Adds `assets: ['resources/images/**', 'resources/fonts/**']` to the Vite Laravel-plugin input (Sage v11.2 pattern).
* Chore: Refresh `brndle.pot` to track the renumbered line references after the i18n + icon swap.

= 1.3.0 =
* New: Native media-library picker (`MediaUpload`) replaces raw URL inputs in hero, content-image-split, team, testimonials, and logos blocks. Includes alt-text input + URL fallback.
* New: Hero block exposes Dark, Light, and Gradient as separate inserter variations instead of a hidden select.
* New: FAQ block emits `FAQPage` JSON-LD schema for rich-result eligibility in search.
* New: Inserter previews (`example`) on all 14 blocks so the inserter shows representative content.
* New: 14 custom landing-page blocks documented (was 8): adds comparison-table, content-image-split, how-it-works, lead-form, team, video-embed.
* New: Hero `gradient` variant now renders (previously declared in `block.json` but unused).
* Improvement: Block editor canvas now loads the compiled `app.css` so Tailwind utilities + theme tokens resolve inside the iframe — fixes invisible dark-on-dark headlines and missing padding in the editor.
* Improvement: Lead form's submit script is now a proper registered viewScript instead of an inline `<script>` per block instance. Adds `aria-live="polite"` status region, focus management on success, safer entity decoding via `DOMParser`.
* Improvement: Editor strings across all 14 blocks are now wrapped in `__()` and a fresh POT contains 1110 msgid entries (was ~30) — translations register via `wp_set_script_translations()`.
* Improvement: Hover transitions, scroll reveals, and the eyebrow ping pulse now respect `prefers-reduced-motion`.
* Improvement: All 14 blocks register Gutenberg-native icons (cover, chartBar, gallery, media, listView, table, tag, quote, people, image, video, help, megaphone, envelope) from `@wordpress/icons` for consistent visual vocabulary.
* Improvement: `version` field added to every `block.json` for cache-busting.
* Improvement: Inline `style="color:#0a0a0a"` removed from hero CTA in favour of the new `.brndle-cta-inverse` class.
* Fix: Comparison-table block previously crashed on render — `@php($expr)` directive containing `===` produced malformed PHP. Now uses block-form `@php ... @endphp`.
* Chore: Bumps `@wordpress/scripts` to 32.x.

= 1.2.4 =
* Fix: Logo strip visibility, FAQ focus outline, and post nav entity encoding.
* Fix: Decode HTML entities in post titles and social share URLs.
* Refactor: Dark-mode toggle becomes a state machine; every surface uses theme tokens.

= 1.2.3 =
* Maintenance release.

= 1.2.2 =
* Maintenance release.

= 1.2.1 =
* Maintenance release.

= 1.2.0 =
* Initial public release.

== Upgrade Notice ==

= 1.4.1 =
**Critical hotfix for 1.4.0.** The 1.4.0 zip was missing `resources/css/critical.css` because the release script excluded the directory it lived in; with the new default `perf_critical_css = true`, fresh installs rendered with no stylesheets at all. 1.4.1 ships the file AND adds a layout safety net so any future asset-missing scenario falls back to rendering with `app.css` instead of breaking. Sites already on 1.4.0 with unstyled pages can either upgrade to 1.4.1, or temporarily toggle Brndle → Performance → Critical CSS off until they upgrade.

= 1.4.0 =
View Transitions and Critical CSS default to ON for fresh installs and never-saved settings — the flip is a no-op for existing sites that have already saved settings. To opt out on a fresh install, set `perf_view_transitions = false` and `perf_critical_css = false` from admin Brndle → Performance. Both features respect `prefers-reduced-motion` and fall back gracefully on unsupported browsers. Also bundles the structural plumbing shipped between 1.3.3 and 1.4.0 (block attribute migration registry, settings schema metadata, Blade compile dry-run script, Playwright E2E journey, workflow templates).

= 1.3.3 =
Adds AVIF / WebP `<picture>` wrapper across every block image, opt-in View Transitions for SPA-feel navigation, opt-in critical CSS to remove render-blocking on first paint, and Lucide icons for frontend Blade. Also lands the structural settings-migration registry needed for any future schema rename. All new features are off by default; turn them on from admin Performance.

= 1.3.2 =
Patch release — performance polish (speculation rules, LCP preload, conditional preconnect on lead-form pages) plus the Heading Scale slider now visibly affects blog content. New CI guardrail catches dead settings before merge. Backward compatible.

= 1.3.1 =
Patch release. Two dead admin settings (Base Font Size, Heading Scale) now actually affect rendered output. `dark:` Tailwind utilities are now bound to the brndle toggle so future dark: usage just works. Block editor iframe loading is simplified to align with upstream Sage. No breaking changes.

= 1.3.0 =
Major editor-experience improvements: native media picker, fixed dark-on-dark headlines in the canvas, FAQ JSON-LD schema, hero variations, and a comparison-table render bug fix. Backward compatible — existing posts keep rendering unchanged.
