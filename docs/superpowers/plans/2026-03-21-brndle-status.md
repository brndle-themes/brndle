# Brndle Theme — Final Status Report

## What's Done & Working

| Feature | Status | Notes |
|---------|--------|-------|
| **Settings Foundation** (5 PHP classes) | DONE | Defaults, ColorPalette, FontPairs, Sanitizer, Settings |
| **REST API** (GET/POST/DELETE + export/import) | DONE | Fixed URL path issue |
| **Admin Panel** (React, 9 tabs) | DONE | Layout keys aligned with templates |
| **Color System** (12 presets + custom) | DONE | Auto-palette from single hex |
| **Font System** (8 pairs) | DONE | Non-blocking Google Fonts loading |
| **Dark Mode** (toggle + system pref) | DONE | Flash prevention via blocking script |
| **Typography Plugin** | DONE | @tailwindcss/typography for prose |
| **Header** (5 styles) | DONE | sticky, solid, transparent, centered, minimal |
| **Footer** (4 styles) | DONE | dark, light, columns, minimal |
| **Blog Archive** (5 layouts) | DONE | grid, list, magazine, editorial, minimal |
| **Single Post** (8 layouts) | DONE | standard through minimal-dark |
| **Landing Blocks** (8) | DONE | hero, stats, features, testimonials, pricing, cta, faq, logos |
| **Block Editor Scripts** | DONE | ServerSideRender + InspectorControls for all 8 |
| **Shared Components** (9) | DONE | progress, TOC, author, related, share, nav, toggle, thumbnail, filter |
| **Logo Upload** | DONE | wp_enqueue_media() + default SVG logos |
| **Performance Settings** | DONE | Now conditional on settings |
| **Plugin Compat** (Yoast, Woo, WPML) | DONE | |
| **Onboarding** | DONE | Setup notice + starter content |
| **CI/CD** | DONE | GitHub Actions for PHP + Node |
| **Developer Hooks** | DONE | 8 filter hooks |
| **Screenshot** | DONE | 1200x900 |

## Fixes Applied This Session

| Fix | What |
|-----|------|
| Blade @php syntax | Split @while/@php onto separate lines |
| Component name mismatch | post-nav → post-navigation, toc → table-of-contents |
| InvokableComponentVariable | Footer socialLinks accessed via callable check |
| Block rendering | Moved from file:./render.blade.php to render_callback via Blade view() |
| Block JSON corruption | Direct DB write bypassing wp_kses content filters |
| CSS var() in Tailwind classes | Replaced with theme utility names (bg-surface-primary etc.) |
| Dark-on-dark text | CTA/hero blocks use explicit text-white for dark sections |
| Tailwind Typography | Installed @tailwindcss/typography for prose styling |
| Admin layout key mismatch | SinglePost.jsx keys aligned with actual template names |
| Performance settings | Made conditional on admin toggles |
| Logo upload | Added wp_enqueue_media() to admin page |
| REST URL | Fixed from brndle/v1/ to brndle/v1/settings |
| Post Composer views | Added partials.archive.* so $readingTime works in archive |
| Orphaned files | Removed 5 unused legacy template files |
| Default logos | Set SVG logos in settings + updated App composer to read from Settings |
| Acorn cache | Safe cache clearing (views only, not parent dirs) |
| PHP platform | Added composer platform config for PHP 8.2 |
| Webpack ESM/CJS | Added .cjs webpack configs with fullySpecified:false |

## Remaining Gaps (Prioritized)

### Must Fix (before v1.0)

| # | Gap | Effort |
|---|-----|--------|
| 1 | `header_mobile_style` setting not wired (admin saves but header ignores) | Small |
| 2 | `footer_columns` setting not used in columns footer style | Small |
| 3 | `archive_show_sidebar` not wired (no sidebar implementation) | Medium |
| 4 | `heading_scale` CSS var output but never consumed | Small |
| 5 | Remaining `[var(--color-*)]` in Tailwind classes (5 files) | Small |
| 6 | Block dark variants use `text-text-secondary` on `bg-surface-inverse` (should use `text-white/60`) | Small |
| 7 | `--color-surface-inverse`, `--color-text-inverse`, `--color-accent-dark` missing from cssVariables() | Small |
| 8 | `--color-border` in cssVariables() but not in @theme (no Tailwind utility generated) | Small |
| 9 | Open Graph meta tags missing when no SEO plugin | Medium |

### Should Fix (v1.1)

| # | Gap |
|---|-----|
| 10 | Breadcrumb rendering in templates (Yoast support declared but no output) |
| 11 | POT file generation for translation |
| 12 | Print stylesheet |
| 13 | Richer 404 page (search form, recent posts) |
| 14 | sidebar-primary widget area for archive |
| 15 | Comments template styling with theme tokens |

### Phase 2

| # | Feature |
|---|---------|
| 16 | Multisite network-wide defaults |
| 17 | Self-hosted Google Fonts |
| 18 | Font metric overrides for CLS |
| 19 | Block patterns (pre-built section combos) |
| 20 | Settings import/export UI buttons in admin |
| 21 | Full WooCommerce Blade templates |
| 22 | Custom post type archive/single support |

## File Count

```
Total files: 115+
PHP classes: 12
Blade templates: 45+
Block definitions: 8 (block.json + Blade render + editor JS)
React components: 19 (admin panel)
CSS files: 3 (app, editor, admin)
Commits: 20+
```

## Build Commands

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build          # Frontend (Vite + Tailwind)
npm run admin:build    # Admin panel (webpack)
npm run blocks:build   # Block editor scripts (webpack)
```
