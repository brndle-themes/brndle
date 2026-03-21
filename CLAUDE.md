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
blocks/                — block.json definitions (8 blocks)
blocks/src/            — Block editor JS (React)
resources/views/       — Blade templates
admin/src/             — React admin panel (9 tabs)
```

## Key Patterns

### Blade Syntax
- Use `@php(function())` for single-line PHP calls
- Use `@php ... @endphp` for multi-line blocks
- NEVER use `@php function() @endphp` on a single line — it breaks Blade compilation

### CSS Colors
- Use Tailwind theme utility names: `bg-surface-primary`, `text-text-primary`, `text-accent`
- NEVER use `text-[var(--color-*)]` or `bg-[var(--color-*)]` — Tailwind v4 doesn't generate these
- For dark sections with `bg-surface-inverse`, use explicit `text-white`, `text-white/60` — NOT `text-text-secondary`

### Block Development
- Blocks render server-side via `render_callback` in BlockServiceProvider
- Block Blade views live in `resources/views/blocks/` (NOT in `blocks/*/`)
- Editor scripts in `blocks/src/` use `ServerSideRender` + `InspectorControls`
- `save: () => null` for all blocks (server-side rendered)

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
- **8 custom blocks**: hero, stats, features, testimonials, pricing, cta, faq, logos
- **12 color schemes**: sapphire, indigo, cobalt, trust, commerce, signal, coral, aubergine, midnight, stone, carbon, neutral
- **8 font pairs**: system, inter, geist, plex, dm-sans, editorial, magazine, humanist

## Cache

After modifying Blade templates, clear the compiled view cache:
```bash
rm -rf wp-content/cache/acorn/framework/views/*
```
Do NOT delete the parent `cache/acorn/` directory — Acorn needs it to exist.

## Skills

Use `/brndle-pages` skill for creating landing pages and configuring sites.
