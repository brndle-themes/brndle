# Brndle

Enterprise WordPress theme with AI-powered landing pages, Blade templating, and Tailwind CSS.

**Zero page builders. Zero JavaScript bloat. Lighthouse 100.**

## Stack

- [Sage](https://roots.io/sage/) architecture (Blade + Acorn)
- [Tailwind CSS](https://tailwindcss.com/) v4 for styling
- [Vite](https://vitejs.dev/) for builds with HMR
- WordPress Block API for section components
- REST API for AI-powered page generation

## Requirements

- PHP >= 8.2
- Node >= 20
- Composer
- WordPress >= 6.6

## Installation

```bash
# Clone
git clone https://github.com/brndle/brndle.git wp-content/themes/brndle

# Install dependencies
cd wp-content/themes/brndle
composer install
npm install

# Build assets
npm run build

# Activate the theme in WordPress
```

## Development

```bash
npm run dev    # Start Vite dev server with HMR
npm run build  # Production build
```

## Architecture

```
brndle/
├── app/                    # PHP application layer
│   ├── Providers/          # Service providers
│   ├── View/Composers/     # View data injection
│   ├── Blocks/             # Block registration
│   ├── setup.php           # Theme setup + WP bloat removal
│   └── filters.php         # WordPress filters
├── blocks/                 # Gutenberg block definitions
│   ├── hero/               # block.json + render.blade.php
│   ├── features/
│   ├── pricing/
│   ├── testimonials/
│   ├── cta/
│   ├── faq/
│   ├── logos/
│   └── stats/
├── resources/
│   ├── views/              # Blade templates
│   │   ├── layouts/        # Master layouts (app, landing)
│   │   ├── sections/       # Header, footer
│   │   └── partials/       # Content partials
│   ├── css/app.css         # Tailwind + design tokens
│   └── js/app.js           # Minimal JS
├── composer.json           # Acorn + PHP deps
├── package.json            # Vite + Tailwind
└── vite.config.js          # Build config
```

## AI Landing Page Creation

Create landing pages via the WordPress REST API:

```bash
curl -X POST https://your-site.com/wp-json/wp/v2/pages \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Product Launch",
    "status": "draft",
    "template": "template-landing",
    "content": "<!-- wp:brndle/hero {\"title\":\"Ship faster\",\"subtitle\":\"Enterprise tools that work.\",\"cta_primary\":\"Get Started\"} /-->\n\n<!-- wp:brndle/features {\"title\":\"Features\",\"features\":[{\"title\":\"AI Powered\",\"description\":\"Smart automation.\"}]} /-->"
  }'
```

## Available Blocks

| Block | Description |
|-------|-------------|
| `brndle/hero` | Full-width hero with eyebrow, CTA, image, logos |
| `brndle/features` | Alternating feature rows with images |
| `brndle/pricing` | 2-3 column pricing table |
| `brndle/testimonials` | Customer testimonial cards |
| `brndle/cta` | Call-to-action banner |
| `brndle/faq` | CSS-only accordion FAQ |
| `brndle/logos` | Trust bar with company logos |
| `brndle/stats` | Key metrics/numbers row |

## Settings

The Brndle Settings panel (`Appearance > Brndle Settings`) provides 9 tabs:

| Tab | Description |
|-----|-------------|
| Colors | Brand primary/secondary colors with 12 preset palettes |
| Typography | 8 curated font pairs (headings + body) |
| Header | Layout style, sticky behavior, CTA button |
| Footer | Column layout, copyright text, social links |
| Blog Archive | 5 archive layouts (grid, list, masonry, cards, minimal) |
| Single Post | 8 single post layouts with reading time, author box, related posts |
| Performance | Asset optimization, lazy loading, preconnect |
| Advanced | Custom CSS/JS injection, code placement |
| Import/Export | Backup and restore all settings as JSON |

Settings are available via the REST API at `brndle/v1/settings` (GET/POST/DELETE) with import/export endpoints.

## Developer Hooks

All settings and output can be customized via WordPress filter hooks:

| Hook | Description |
|------|-------------|
| `brndle/settings` | Filter all resolved settings before use |
| `brndle/color_presets` | Add or modify the available color presets |
| `brndle/color_palette` | Modify the generated CSS color palette |
| `brndle/font_pairs` | Add or modify the available font pairs |
| `brndle/css_variables` | Inject custom CSS variables into the root stylesheet |
| `brndle/archive_layout` | Override the archive layout programmatically |
| `brndle/single_layout` | Override the single post layout per-post |
| `brndle/settings_defaults` | Modify the default settings values |

### Example: Add a Custom Color Preset

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

### Example: Override Archive Layout for a Category

```php
add_filter('brndle/archive_layout', function (string $layout): string {
    if (is_category('news')) {
        return 'list';
    }
    return $layout;
});
```

## Plugin Compatibility

Brndle includes built-in compatibility layers for popular plugins:

### Yoast SEO / RankMath

- Automatic breadcrumb support when either plugin is active
- Fallback Article JSON-LD schema when no SEO plugin is detected
- Schema includes headline, dates, author, publisher, image, and description

### WooCommerce

- Full `woocommerce` theme support with product gallery zoom, lightbox, and slider
- WooCommerce content wrapped in Brndle's layout container (`max-w-7xl`)
- Default WooCommerce wrappers replaced with theme-consistent markup

### WPML / Polylang

- Automatic `hreflang` tag output when WPML or Polylang is active
- Proper alternate link tags for all available languages

## Color Schemes

12 built-in color presets:

| Preset | Primary | Secondary |
|--------|---------|-----------|
| Indigo Night | `#6366f1` | `#1e1b4b` |
| Ocean | `#0ea5e9` | `#0c4a6e` |
| Forest | `#22c55e` | `#14532d` |
| Sunset | `#f97316` | `#7c2d12` |
| Rose | `#f43f5e` | `#4c0519` |
| Violet | `#8b5cf6` | `#2e1065` |
| Amber | `#f59e0b` | `#78350f` |
| Teal | `#14b8a6` | `#134e4a` |
| Slate | `#64748b` | `#0f172a` |
| Zinc | `#71717a` | `#18181b` |
| Stone | `#78716c` | `#1c1917` |
| Neutral | `#737373` | `#171717` |

## Font Pairs

8 curated font combinations:

| Pair | Heading | Body | Source |
|------|---------|------|--------|
| Modern | Inter | Inter | Google Fonts |
| Classic | Playfair Display | Source Sans 3 | Google Fonts |
| Technical | JetBrains Mono | Inter | Google Fonts |
| Editorial | Cormorant Garamond | Proza Libre | Google Fonts |
| Geometric | Poppins | Work Sans | Google Fonts |
| Humanist | Nunito | Merriweather | Google Fonts |
| Minimal | DM Sans | DM Sans | Google Fonts |
| System | System UI | System UI | System Stack |

## License

GPL-2.0-or-later
