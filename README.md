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

## License

GPL-2.0-or-later
