=== Brndle ===
Contributors: brndlethemes
Tags: blog, custom-logo, custom-menu, featured-images, full-width-template, theme-options, translation-ready
Tested up to: 6.8
Stable tag: 1.3.1
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

= 1.3.1 =
Patch release. Two dead admin settings (Base Font Size, Heading Scale) now actually affect rendered output. `dark:` Tailwind utilities are now bound to the brndle toggle so future dark: usage just works. Block editor iframe loading is simplified to align with upstream Sage. No breaking changes.

= 1.3.0 =
Major editor-experience improvements: native media picker, fixed dark-on-dark headlines in the canvas, FAQ JSON-LD schema, hero variations, and a comparison-table render bug fix. Backward compatible — existing posts keep rendering unchanged.
