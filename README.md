# Brndle

Free, open-source WordPress theme for agencies. One theme, unlimited client sites.

**12 color schemes. 8 font pairs. 8 header styles. 6 footer styles. Dark mode. AI landing pages. Lighthouse 100.**

Built on [Sage](https://roots.io/sage/) + [Acorn](https://roots.io/acorn/) (Laravel Blade + Tailwind CSS v4 + Vite 7).

## What's Included

### Design System

| Category | Options |
|----------|---------|
| **Color Schemes** | Sapphire, Indigo, Cobalt, Trust, Commerce, Signal, Coral, Aubergine, Midnight, Stone, Carbon, Neutral |
| **Font Pairs** | System UI, Inter, Geist Sans, IBM Plex, DM Sans, Editorial, Magazine, Humanist |
| **Header Styles** | Sticky, Solid, Transparent, Centered, Minimal, Split, Banner, Glass |
| **Footer Styles** | Dark, Light, Columns, Minimal, Big, Stacked |
| **Archive Layouts** | Grid, List, Magazine, Editorial, Minimal |
| **Post Layouts** | Standard, Hero Immersive, Sidebar, Editorial, Cinematic, Presentation, Split, Minimal Dark |
| **Page Templates** | Default, Landing Page, Full Canvas, Transparent Header |
| **Dark Mode** | Toggle, system preference, or disabled entirely |

### Landing Page Blocks

14 custom Gutenberg blocks for building landing pages — all server-side rendered, all WP 6.9+ ready (`apiVersion: 3`).

| Block | Description |
|-------|-------------|
| `brndle/hero` | Full-width hero with eyebrow, CTAs, image (media picker), logo strip. Dark / Light / Gradient inserter variations. |
| `brndle/stats` | Key metrics row (e.g. "100 Lighthouse Score", "0 KB JS"). |
| `brndle/features` | Alternating feature rows with images and bullet points. |
| `brndle/content-image-split` | Side-by-side content + media block with media-library picker. |
| `brndle/how-it-works` | Numbered process steps, horizontal or vertical layout. |
| `brndle/comparison-table` | Feature comparison grid with optional highlighted column. |
| `brndle/pricing` | 2–3 column pricing table with featured-plan highlighting. |
| `brndle/testimonials` | Customer testimonial cards with avatars (media picker) and star ratings. |
| `brndle/team` | Team member cards with photo (media picker), role, bio, social links. |
| `brndle/logos` | Trust bar with company logos (media picker) or text fallback. |
| `brndle/video-embed` | YouTube / Vimeo / self-hosted video wrapper. |
| `brndle/faq` | Accessible accordion FAQ with ARIA attributes — emits `FAQPage` JSON-LD. |
| `brndle/cta` | Call-to-action banner with primary / secondary buttons. |
| `brndle/lead-form` | Email-capture form with REST submission, aria-live status, Mailchimp passthrough. |

### Admin Panel

Settings panel at **Brndle** in the WP admin sidebar with tabs:

- **Site Identity** — Logo (light/dark), social links
- **Colors** — 12 presets + custom accent color
- **Dark Mode** — Toggle on/off, position, default mode
- **Typography** — 8 font pairs, base size, heading scale
- **Header** — 8 styles, CTA button, mobile menu, announcement banner
- **Footer** — 6 styles, copyright, column menus, social links
- **Blog Archive** — Layout, posts per page, sidebar, category filter
- **Single Post** — Layout, progress bar, reading time, author box, related posts, TOC
- **Performance** — Remove emoji/embed scripts, lazy images, preload fonts

All settings accessible via REST API at `brndle/v1/settings` (GET/POST/DELETE) with import/export.

### Plugin Compatibility

Built-in support for:
- **Yoast SEO / RankMath** — Breadcrumbs, fallback JSON-LD schema
- **WooCommerce** — Gallery zoom/lightbox/slider, theme-consistent wrappers
- **WPML / Polylang** — Automatic hreflang tags

## Requirements

- WordPress 6.6+
- PHP 8.2+
- Node 20+ (for development)
- Composer (for development)

## Installation

### From Release Zip

1. Download `brndle-x.x.x.zip` from [Releases](https://github.com/brndle-themes/brndle/releases)
2. Go to **Appearance → Themes → Add New → Upload Theme**
3. Upload the zip and activate

No build tools needed — the release zip includes compiled assets.

### From Source (Development)

```bash
git clone https://github.com/brndle-themes/brndle.git wp-content/themes/brndle
cd wp-content/themes/brndle
composer install
npm install
npm run build && npm run admin:build && npm run blocks:build
```

Activate the theme in WordPress.

## Development

```bash
npm run dev              # Vite dev server with HMR
npm run build            # Production frontend build (Tailwind + Vite)
npm run admin:build      # Admin settings panel (React + webpack)
npm run blocks:build     # Block editor scripts (React + webpack)
```

After modifying Blade templates, clear the compiled view cache:

```bash
rm -rf wp-content/cache/acorn/framework/views/*
```

### Building a Release

```bash
./bin/release.sh 1.0.0
```

Creates `brndle-1.0.0.zip` with compiled assets, no source files or dev dependencies.

## Architecture

```
brndle/
├── app/
│   ├── Compatibility/     # Yoast, WooCommerce, WPML support
│   ├── Onboarding/        # Setup notice + starter content
│   ├── Providers/         # ThemeServiceProvider, BlockServiceProvider, SettingsServiceProvider
│   ├── Settings/          # Settings, Defaults, ColorPalette, FontPairs, Sanitizer
│   ├── View/Composers/    # App, Post, Theme (Blade data injection)
│   ├── setup.php          # Theme setup, nav menus, image sizes
│   └── filters.php        # WordPress filters
├── admin/src/             # React admin settings panel (9 tabs)
├── blocks/                # block.json definitions (14 blocks)
├── blocks/src/            # Block editor JS (ServerSideRender + InspectorControls)
├── resources/
│   ├── views/             # Blade templates
│   │   ├── layouts/       # app.blade.php, landing.blade.php
│   │   ├── sections/      # header.blade.php, footer.blade.php
│   │   ├── blocks/        # Server-rendered block views
│   │   └── partials/      # Components, archive layouts, post layouts
│   ├── css/app.css        # Tailwind v4 + theme tokens + dark mode
│   └── js/                # Minimal JS (app.js, editor.js)
├── bin/release.sh         # Release zip builder
├── style.css              # WordPress theme header
├── functions.php          # Acorn bootstrap
└── theme.json             # Block editor settings
```

### Key Patterns

- **Blade syntax**: Use `@php(func())` for single-line, `@php ... @endphp` for multi-line
- **CSS colors**: Use Tailwind theme utilities (`bg-surface-primary`, `text-accent`) — never `text-[var(--color-*)]`
- **Dark sections**: Use `brndle-section-dark` class — always dark regardless of dark mode toggle
- **Blocks**: Render via `render_callback` in BlockServiceProvider, views in `resources/views/blocks/`
- **Settings**: `Settings::get('key', default)` — stored in `wp_options` key `brndle_settings`

## Developer Hooks

| Hook | Description |
|------|-------------|
| `brndle/settings` | Filter all resolved settings |
| `brndle/color_presets` | Add or modify color presets |
| `brndle/color_palette` | Modify generated CSS color palette |
| `brndle/font_pairs` | Add or modify font pairs |
| `brndle/css_variables` | Inject custom CSS variables |
| `brndle/archive_layout` | Override archive layout |
| `brndle/single_layout` | Override single post layout |
| `brndle/settings_defaults` | Modify default settings |

### Example: Custom Color Preset

```php
add_filter('brndle/color_presets', function (array $presets): array {
    $presets['brand'] = [
        'label'     => 'My Brand',
        'primary'   => '#FF6B00',
        'secondary' => '#1A1A2E',
    ];
    return $presets;
});
```

## AI Landing Pages

Brndle integrates with [Claude Code](https://claude.ai/claude-code) to generate complete landing pages from a text description. Pages are created as standard WordPress block markup — no lock-in.

```bash
# In Claude Code, from the theme directory:
/brndle-pages
```

Or create pages via the REST API:

```bash
curl -X POST https://your-site.com/wp-json/wp/v2/pages \
  -H "Authorization: Bearer TOKEN" \
  -d '{"title":"Launch Page","status":"draft","content":"<!-- wp:brndle/hero {\"title\":\"Ship faster\"} /-->"}'
```

## License

GPL-2.0-or-later — same license as WordPress.

Free to use, modify, and distribute. See [LICENSE](LICENSE) for details.
