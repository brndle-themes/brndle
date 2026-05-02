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
- **14 custom blocks**: hero, stats, features, testimonials, pricing, cta, faq, logos, content-image-split, how-it-works, lead-form, comparison-table, team, video-embed
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
