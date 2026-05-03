# Brndle Theme — Claude Code Instructions

## What This Is

Enterprise WordPress theme built on Sage/Acorn (Laravel Blade + Tailwind CSS v4 + Vite). Designed for 100+ client sites with AI-powered landing page creation.

## Tech Stack

- **PHP 8.2+**, Roots Acorn 5 (Laravel-in-WordPress)
- **Blade** templating with View Composers
- **Tailwind CSS v4** with `@tailwindcss/typography`
- **Vite 7** for frontend builds
- **React** (`@wordpress/scripts`) for admin panel + block editor
- **WordPress REST API** for settings + page creation

## Architecture

```
app/Settings/          — Settings system (Defaults, ColorPalette, FontPairs, Sanitizer, Settings)
app/Providers/         — Service providers (Theme, Block, Settings)
app/View/Composers/    — Blade data injection (App, Post, Theme)
app/Compatibility/     — Plugin compat (Yoast, WooCommerce, WPML)
app/Onboarding/        — Setup notice + starter content
blocks/                — block.json definitions (14 blocks)
blocks/src/            — Block editor JS (React)
resources/views/       — Blade templates
admin/src/             — React admin panel (9 tabs)
```

## Key Patterns

### Blade Syntax
- Use `@php(function())` for single-line PHP calls
- Use `@php ... @endphp` for multi-line blocks
- NEVER use `@php function() @endphp` on a single line — it breaks Blade compilation
- Avoid `@php($expr)` when `$expr` contains `===` or other operators with `=` — the Acorn compiler emits malformed PHP for those (bit us on comparison-table.blade.php). Use the multi-line `@php ... @endphp` block instead

### CSS Colors
- Use Tailwind theme utility names: `bg-surface-primary`, `text-text-primary`, `text-accent`
- NEVER use `text-[var(--color-*)]` or `bg-[var(--color-*)]` — Tailwind v4 doesn't generate these
- For dark sections with `bg-surface-inverse`, use explicit `text-white`, `text-white/60` — NOT `text-text-secondary`

### Icons (no emoji as UI affordance)
- **Editor JSX** (admin / block editor): import from `@wordpress/icons`. Looks native to the WP editor and the dependency-extraction-webpack-plugin tree-shakes per-import (verified — only imported icons end up in the bundle).
- **Frontend Blade**: use Lucide via `<x-icon name="kebab-case-name" />`. The component reads from `resources/icons/{name}.svg`, populated at `npm run build` time by `bin/copy-lucide-icons.mjs` from a curated list. Add a new icon: append to the ICONS array in that script, run `npm run icons:copy`, commit the SVG.
- **Never use emoji** as a UI affordance (stars, arrows, hashes, dots). Emoji rendering varies by OS / browser, breaks visual rhythm, ignores theme colors. Use Lucide.
- **Never paste raw `<svg>` markup** into Blade templates — go through `<x-icon>` so the design language stays consistent.

### Block Development
- Blocks render server-side via `render_callback` in `BlockServiceProvider`
- Block Blade views live in `resources/views/blocks/` (NOT in `blocks/*/`)
- Editor scripts in `blocks/src/` use `ServerSideRender` + `InspectorControls`
- `save: () => null` for all blocks (server-side rendered)
- All blocks at `apiVersion: 3` (WP 6.9+ iframe-editor compatible)
- Editor JSX strings use `__('...', 'brndle')` and `wp_set_script_translations()` is called in `BlockServiceProvider`
- Block icons import from `@wordpress/icons` (not inline `<svg>`); see `blocks/src/*.js` for the per-block mapping
- Each `block.json` carries an `example` for inserter previews and a `version` field
- For image inputs, use the shared `<ImageControl>` from `./components/image-control` (MediaUpload + URL fallback + alt text). Store as `*_id` (number) + `*_alt` (string) alongside the existing URL string attribute
- FAQ block emits `FAQPage` JSON-LD schema for SEO
- Hero exposes Dark / Light / Gradient as `variations` in `block.json`
- Lead form's submit JS lives in `blocks/src/lead-form-view.js` (registered as `brndle-lead-form-view`, lazily enqueued from the render callback)
- The compiled frontend `app.css` is enqueued inside the block editor iframe via `enqueue_block_assets` (gated on `is_admin()`) so Tailwind utilities + `.brndle-section-*` rules resolve in the canvas
- Hover/animation utilities use Tailwind's `motion-reduce:` variant; a global `prefers-reduced-motion` rule in `app.css` neutralises transitions inside `.brndle-section-dark`, `.brndle-section-gradient`, and any `main > section[class*="brndle-"]` (Blade renders its own `<section>` so core's `.wp-block-brndle-*` wrapper class is never present)

### Settings
- All settings in `wp_options` key `brndle_settings`
- `Settings::get('key', default)` to read
- CSS variables injected in `<head>` via `Settings::cssVariables()`
- REST API: GET/POST/DELETE at `brndle/v1/settings`

### Social Links in Templates
Social links from Theme composer are `InvokableComponentVariable`:
```blade
@php($links = is_callable($socialLinks) ? $socialLinks() : (is_array($socialLinks) ? $socialLinks : []))
```

## Build Commands

```bash
npm run build          # Frontend (Vite + Tailwind)
npm run admin:build    # Admin settings panel
npm run blocks:build   # Block editor scripts
./bin/release.sh 1.0.0 # Build release zip
```

## What's Available

- **8 header styles**: sticky, solid, transparent, centered, minimal, split, banner, glass
- **6 footer styles**: dark, light, columns, minimal, big, stacked
- **5 archive layouts**: grid, list, magazine, editorial, minimal
- **8 single post layouts**: standard, hero-immersive, sidebar, editorial, cinematic, presentation, split, minimal-dark
- **4 page templates**: default, template-landing, template-canvas, template-transparent
- **18 custom blocks**:
  - **Marketing/landing (14)**: hero, stats, features, testimonials, pricing, cta, faq, logos, content-image-split, how-it-works, lead-form, comparison-table, team, video-embed
  - **Editorial (4, v2.1+)**: code (syntax-highlighted with copy + line numbers), pull-quote (3 variants), timeline (3 icon styles), tabs-accordion (combined, WAI-ARIA tabs + disclosure)
- **12 color schemes**: sapphire, indigo, cobalt, trust, commerce, signal, coral, aubergine, midnight, stone, carbon, neutral
- **8 font pairs**: system, inter, geist, plex, dm-sans, editorial, magazine, humanist

## Cache

After modifying Blade templates, clear the compiled view cache. The path is
relative to the WordPress install root (`app/public/`), not the theme dir:

```bash
rm -rf /Users/varundubey/Local\ Sites/elementor/app/public/wp-content/cache/acorn/framework/views/*.php
```

Or from the theme directory: `rm -rf ../../cache/acorn/framework/views/*.php`

Do NOT delete the parent `cache/acorn/` directory — Acorn needs it to exist.

## Releases

The release script lives at `bin/release.sh`. Bump `Version:` in `style.css`
and `Stable tag:` + Changelog in `readme.txt`, commit + merge to `main`, then:

```bash
./bin/release.sh 1.x.y
git tag -a v1.x.y origin/main -m "..."
git push origin v1.x.y
gh release create v1.x.y brndle-1.x.y.zip --title "v1.x.y — ..." --notes "..."
```

The GitHub repo blocks direct `git push origin main`. Use `gh pr merge --squash
--delete-branch` to land work; `gh pr merge` uses the API and is allowed.

## Recent Changes

| Version | Date | Highlights |
|---------|------|------------|
| 2.1.0 | 2026-05-04 | **Editorial block bundle.** 4 new blocks (code, pull-quote, timeline, tabs-accordion) bring library to 18. apiVersion 3, server-rendered Blade, scoped CSS, lazy view-script enqueue, full WAI-ARIA on interactive surfaces, prefers-reduced-motion safe. Code block lazy-loads highlight.js from CDN only when in viewport. Tabs/accordion is one block with displayMode toggle. Pull-quote has 3 variants registered as block.json variations. Timeline has dot/numbered/lucide icon styles + intersection-observer reveal. AI docs at `.claude/skills/brndle-pages.md` cover all 4. Plan at `plans/2026-05-04-v2.1-editorial-blocks.md`. |
| 2.0.0 | 2026-05-03 | **Audit baseline.** Yoast/Rank Math Person schema enrichment (theme stops emitting Article/BreadcrumbList/Person — defers to plugin), Brndle-styled comments template + Walker, 404 polish (search + recent posts), search polish (count + topic chips), back-to-top floating button, print stylesheet, last-updated date pill on single posts. Plan at `plans/2026-05-03-theme-audit-roadmap.md` (v2.0 bundle, 8.5h). |
| 1.5.8 | 2026-05-03 | Admin panel switched to fluid `max-width: 90%` + auto margins (was pixel-capped) so it scales with the viewport |
| 1.5.7 | 2026-05-03 | Removed silent `array_slice` truncation in 4 of 7 section styles — count slider now honored end-to-end |
| 1.5.6 | 2026-05-03 | Ticker scrollbar replaced with overlay arrow buttons (vanilla JS, hides on touch / at scroll boundaries); large-site perf hardening (auto-cats transient cache, thumbnail prime per section, 12-section cap, REST `_fields` trim) |
| 1.5.5 | 2026-05-03 | Hotfix: `ReferenceError: sections` crashed the admin in 1.5.4. Restored the missing `const sections = ...` declaration |
| 1.5.4 | 2026-05-03 | Dropped alternating section background tint (read as card-within-card on dark themes); kept dividers + numbered kickers |
| 1.5.3 | 2026-05-03 | Admin pre-populates 5 sections on toggle off→on transition; "Apply suggested defaults" button; `!important` tab strip nowrap |
| 1.5.2 | 2026-05-02 | Auto-defaults for sections + visual variation (5 cats, magazine flow, kicker numerals, hide redundant page header) |
| 1.5.1 | 2026-05-02 | Admin polish: `decodeEntities()` on REST category labels, panel widened (later replaced with fluid 90%), tab strip nowrap |
| 1.5.0 | 2026-05-02 | New: Blog Homepage Sections layout. Toggle to render stacked category sections (news-portal style) instead of single archive layout when blog is the front page. 7 styles: featured-hero, grid-3col, magazine-strip, list-with-thumb, mixed-2x2, ticker, editorial-pair. Plan: `plans/2026-05-02-blog-homepage-sections.md` |
| 1.4.2 | 2026-05-02 | Wrapped `critical.css` in `@layer base` / `@layer utilities` so Tailwind's `.hidden` properly overrides the preflight `img { display: block }` (fixes the duplicate-logo bug seen on attowp.com) |
| 1.4.1 | 2026-05-02 | Hotfix: keep `critical.css` in the release zip; layout falls back to render-blocking `app.css` if critical inline can't be read |
| 1.4.0 | 2026-05-02 | Defaulted `perf_view_transitions` + `perf_critical_css` ON for fresh installs; block attribute migration registry; settings schema metadata; Blade compile dry-run CI; E2E journey test |
| 1.3.0 | 2026-05-02 | Block quality pass — editor canvas styles, MediaUpload picker, FAQ JSON-LD, hero variations, i18n, lead-form view script, `@wordpress/icons`, wp-scripts v32, comparison-table compile fix |
| 1.2.4 | 2026-04-14 | Logo strip visibility, FAQ focus outline, post nav entity encoding, dark-mode toggle state machine |

## Upstream tracking (roots/sage)

Brndle is built on Sage / Acorn. Stay aware of upstream — the framework
moves and our deps drift.

Run the helper before any framework upgrade work:

```bash
./bin/check-upstream.sh
```

It prints a side-by-side table (PHP, Acorn, Vite, vite plugins) plus the
latest Sage release tag and last five commits to `roots/sage`.

### Known gap as of 2026-05-02 (brndle 1.3.0 → sage v11.2.1)

| dep | brndle | sage main | upgrade priority |
|-----|--------|-----------|------------------|
| `roots/acorn` | `^5.0` | `^6.0` | **High** — Acorn 6 may fix the `@php($expr)===` Blade compiler bug we hit on `comparison-table.blade.php` |
| `vite` | `^7.0` | `^8.0` | Medium — pulls `laravel-vite-plugin@3` and `@roots/vite-plugin@2` together |
| `laravel-vite-plugin` | `^2.0` | `^3.0` | (bundled with Vite 8) |
| `@roots/vite-plugin` | `^1.0` | `^2.0` | (bundled with Vite 8) |
| PHP | `>=8.2` | `>=8.3` | Low — only raise if Acorn 6 forces it |

### Cadence

- Run `bin/check-upstream.sh` at the start of any framework-touching session
- Skim the Sage releases page before cutting a brndle minor version
- A behaviour bug we can't reproduce in isolation may already be fixed
  upstream — check before going deep

Reference: <https://github.com/roots/sage/releases>

## Skills

Use `/brndle-pages` skill for creating landing pages and configuring sites.
