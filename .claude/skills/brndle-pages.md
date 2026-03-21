---
name: brndle-pages
description: Create landing pages, blog posts, and configure sites for Brndle WordPress theme. Use when asked to create pages, landing pages, blog posts, configure theme settings, or manage any site using the Brndle theme.
---

# Brndle Page Builder & Site Manager

Create WordPress pages/posts and configure Brndle theme sites. Content is stored as standard WordPress block markup in `post_content`.

## How It Works

1. User describes the page or site configuration they want
2. Generate block markup combining Brndle section blocks + WordPress core blocks
3. Create the page via WordPress REST API, WP-CLI, or direct PHP
4. Configure theme settings (colors, fonts, header/footer style) via REST API

## Page Templates

Brndle has 4 page templates. Set via `update_post_meta($id, '_wp_page_template', 'template-name');`

| Template | Slug | Use Case |
|----------|------|----------|
| **Default** | (none) | Standard page — header + prose content + footer |
| **Landing Page** | `template-landing` | Full-width block sections with header + footer. Use for marketing/product pages. |
| **Full Canvas** | `template-canvas` | Zero chrome — no header, no footer, no padding. Pure content. Use for custom creative pages, app-like experiences. |
| **Transparent Header** | `template-transparent` | Header floats over content with transparent bg, turns solid on scroll. Use for pages with dark hero sections. |

## Header Styles (8)

Set via admin panel or REST API: `header_style` setting.

| Style | Description | Best For |
|-------|-------------|----------|
| `sticky` | Glassmorphism sticky header, bg-blur on scroll | Default, most sites |
| `solid` | Static, solid white bg, thick border | Corporate, traditional |
| `transparent` | Fixed, transparent bg → solid on scroll, white text | Dark hero landing pages |
| `centered` | Logo centered top, nav pills below | Editorial, magazine |
| `minimal` | No visible header, floating hamburger → fullscreen overlay | Design agencies, portfolios |
| `split` | Two rows: logo+CTA top, nav bar below | News, content-heavy sites |
| `banner` | Announcement bar above sticky header | E-commerce, promos |
| `glass` | Fixed glassmorphism, gradient top border, white text | Premium SaaS, modern |

## Footer Styles (6)

Set via admin panel or REST API: `footer_style` setting.

| Style | Description | Best For |
|-------|-------------|----------|
| `dark` | Dark bg, single row: logo / copyright / social | Default, clean |
| `light` | Light bg, same layout | Minimal, light themes |
| `columns` | Dark bg, 4-column grid (brand + 3 menu columns) + bottom bar | Enterprise, content-rich |
| `minimal` | Thin top border, centered copyright only | Single-page, landing |
| `big` | Newsletter signup + 4-col grid + bottom bar | SaaS, enterprise |
| `stacked` | Everything centered vertically: logo → tagline → nav → social → copyright | Clean, single-page |

## Available Blocks

### Brndle Custom Blocks (Landing Page Sections)

Full-width section blocks rendered server-side via Blade + Tailwind.

#### `brndle/hero` — Full-width hero section
```
<!-- wp:brndle/hero {"eyebrow":"","title":"","subtitle":"","cta_primary":"","cta_primary_url":"#","cta_secondary":"","cta_secondary_url":"#","image":"","variant":"dark","logos":["Company1","Company2"]} /-->
```
- `variant`: "dark" | "light" | "gradient"
- `logos`: array of strings or `{"url":"img.png","name":"Company"}` objects
- `image`: URL for product screenshot below hero

#### `brndle/stats` — Key metrics row
```
<!-- wp:brndle/stats {"items":[{"value":"100","label":"Lighthouse Score"},{"value":"0 KB","label":"JavaScript"}],"variant":"light"} /-->
```

#### `brndle/features` — Alternating feature sections
```
<!-- wp:brndle/features {"eyebrow":"Features","title":"","subtitle":"","features":[{"title":"","description":"","bullets":["bullet1","bullet2"],"image":"","icon":""}],"variant":"light"} /-->
```
- Features alternate left/right automatically

#### `brndle/testimonials` — Customer testimonial cards
```
<!-- wp:brndle/testimonials {"eyebrow":"Testimonials","title":"","items":[{"quote":"","name":"","role":"","avatar":"","stars":5}]} /-->
```

#### `brndle/pricing` — Pricing table
```
<!-- wp:brndle/pricing {"eyebrow":"Pricing","title":"","subtitle":"","plans":[{"name":"","description":"","price":"$99","period":"/mo","features":["feat1","feat2"],"cta_text":"Get Started","cta_url":"#","featured":false,"badge":""}]} /-->
```
- `featured: true` highlights with dark bg + accent border
- `badge`: text above featured plan (e.g., "Most Popular")

#### `brndle/cta` — Call-to-action banner
```
<!-- wp:brndle/cta {"title":"","subtitle":"","cta_primary":"","cta_primary_url":"#","cta_secondary":"","cta_secondary_url":"#","variant":"dark"} /-->
```

#### `brndle/faq` — Accordion FAQ
```
<!-- wp:brndle/faq {"title":"Frequently asked questions","items":[{"question":"","answer":""}]} /-->
```

#### `brndle/logos` — Trust/client logo strip
```
<!-- wp:brndle/logos {"title":"Trusted by","companies":["Stripe","Vercel","Linear"],"variant":"light"} /-->
```

### WordPress Core Blocks (Content Building)

#### Layout
```html
<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group"><!-- inner blocks --></div>
<!-- /wp:group -->

<!-- wp:columns -->
<div class="wp-block-columns">
  <!-- wp:column {"width":"66.66%"} -->
  <div class="wp-block-column" style="flex-basis:66.66%"><!-- content --></div>
  <!-- /wp:column -->
  <!-- wp:column {"width":"33.33%"} -->
  <div class="wp-block-column" style="flex-basis:33.33%"><!-- content --></div>
  <!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"80px"} -->
<div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->
```

#### Text
```html
<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Text here.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
  <!-- wp:list-item -->
  <li>Item</li>
  <!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><p>Quote</p><cite>Source</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:code -->
<pre class="wp-block-code"><code>code</code></pre>
<!-- /wp:code -->

<!-- wp:details -->
<details class="wp-block-details">
  <summary>Click to expand</summary>
  <!-- wp:paragraph -->
  <p>Hidden content</p>
  <!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
```

#### Media
```html
<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large">
  <img src="URL" alt="Description"/>
</figure>
<!-- /wp:image -->

<!-- wp:cover {"url":"URL","dimRatio":60,"overlayColor":"black","minHeight":500} -->
<div class="wp-block-cover" style="min-height:500px">
  <span aria-hidden="true" class="wp-block-cover__background has-black-background-color has-background-dim-60 has-background-dim"></span>
  <img class="wp-block-cover__image-background" alt="" src="URL"/>
  <div class="wp-block-cover__inner-container">
    <!-- wp:heading {"textAlign":"center"} -->
    <h2 class="wp-block-heading has-text-align-center">Title</h2>
    <!-- /wp:heading -->
  </div>
</div>
<!-- /wp:cover -->

<!-- wp:media-text {"mediaUrl":"URL","mediaType":"image"} -->
<div class="wp-block-media-text is-stacked-on-mobile">
  <div class="wp-block-media-text__content">
    <!-- wp:paragraph --><p>Text</p><!-- /wp:paragraph -->
  </div>
  <figure class="wp-block-media-text__media"><img src="URL" alt=""/></figure>
</div>
<!-- /wp:media-text -->

<!-- wp:gallery {"columns":3} -->
<figure class="wp-block-gallery has-nested-images columns-3 is-cropped">
  <!-- wp:image {"sizeSlug":"large"} -->
  <figure class="wp-block-image size-large"><img src="URL" alt=""/></figure>
  <!-- /wp:image -->
</figure>
<!-- /wp:gallery -->

<!-- wp:video {"src":"URL"} -->
<figure class="wp-block-video"><video controls src="URL"></video></figure>
<!-- /wp:video -->

<!-- wp:embed {"url":"https://youtube.com/watch?v=ID","type":"video","providerNameSlug":"youtube"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube">
  <div class="wp-block-embed__wrapper">https://youtube.com/watch?v=ID</div>
</figure>
<!-- /wp:embed -->
```

#### Buttons
```html
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">
  <!-- wp:button -->
  <div class="wp-block-button">
    <a class="wp-block-button__link wp-element-button" href="#">Primary</a>
  </div>
  <!-- /wp:button -->
  <!-- wp:button {"className":"is-style-outline"} -->
  <div class="wp-block-button is-style-outline">
    <a class="wp-block-button__link wp-element-button" href="#">Secondary</a>
  </div>
  <!-- /wp:button -->
</div>
<!-- /wp:buttons -->
```

#### Dynamic
```html
<!-- wp:latest-posts {"postsToShow":3,"displayPostDate":true,"displayFeaturedImage":true,"postLayout":"grid","columns":3} /-->

<!-- wp:social-links {"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links">
  <!-- wp:social-link {"url":"https://twitter.com/handle","service":"twitter"} /-->
  <!-- wp:social-link {"url":"https://github.com/handle","service":"github"} /-->
</ul>
<!-- /wp:social-links -->
```

## Page Creation

### Via REST API
```bash
curl -X POST https://site.com/wp-json/wp/v2/pages \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Page Title",
    "status": "draft",
    "content": "BLOCK_MARKUP_HERE"
  }'

# Set template:
curl -X POST https://site.com/wp-json/wp/v2/pages/ID \
  -d '{"meta":{"_wp_page_template":"template-landing"}}'
```

### Via PHP
```php
remove_filter('content_save_pre', 'wp_filter_post_kses');
$id = wp_insert_post([
    'post_title' => 'Title',
    'post_content' => $block_content,
    'post_status' => 'publish',
    'post_type' => 'page',
]);
update_post_meta($id, '_wp_page_template', 'template-landing');
```

## Content Rules

1. JSON in block comments: use `\"key\":\"value\"` (escaped quotes)
2. **No raw HTML** in JSON attribute values — breaks block parser
3. Self-closing blocks: `<!-- wp:name {...} /-->`
4. Container blocks: `<!-- wp:name -->...<!-- /wp:name -->`
5. Double newline between blocks
6. Straight quotes only (`"`) — no curly/smart quotes
7. Set `_wp_page_template` meta for non-default templates

## Typical Page Structures

### SaaS Landing Page
```
Hero (dark, with eyebrow + CTAs + logos)
Stats (light, 3-4 metrics)
Features (light, 3 features with bullets)
Testimonials (light, 3 testimonials)
Pricing (light, 2-3 plans)
FAQ (light, 4-6 questions)
CTA (dark)
```
Template: `template-landing`

### Agency/Portfolio Page
```
Hero (dark, minimal text)
Gallery or Media+Text sections (core blocks)
Testimonials
CTA
```
Template: `template-transparent` or `template-canvas`

### Product Page
```
Hero (light/dark, with product image)
Features (with product screenshots)
Stats (social proof numbers)
Testimonials
Pricing (single plan or comparison)
FAQ
CTA
```
Template: `template-landing`

### About Page
Use default template with core blocks:
```
Cover (team photo)
Heading + Paragraphs
Columns (team members)
Spacer
Quote (mission statement)
```

### Blog Post
Standard core blocks only:
```
Heading (h2)
Paragraph(s)
Image
Heading (h2)
Paragraph(s)
List
Quote / Code (optional)
```

## Site Configuration REST API

### Read/Write Settings
```
GET  /wp-json/brndle/v1/settings         — read all
POST /wp-json/brndle/v1/settings         — save (merge)
DELETE /wp-json/brndle/v1/settings       — reset to defaults
GET  /wp-json/brndle/v1/settings/export  — export JSON
POST /wp-json/brndle/v1/settings/import  — import JSON
```

### All Setting Keys
```json
{
  "color_scheme": "sapphire",
  "custom_accent": "",
  "dark_mode_default": "light",
  "dark_mode_toggle": true,
  "dark_mode_toggle_position": "bottom-right",
  "font_pair": "inter",
  "font_size_base": 16,
  "heading_scale": 1.25,
  "header_style": "sticky",
  "header_cta_text": "",
  "header_cta_url": "",
  "header_banner_text": "",
  "header_mobile_style": "slide",
  "footer_style": "dark",
  "footer_columns": 3,
  "footer_copyright": "",
  "footer_show_social": true,
  "site_logo_light": "",
  "site_logo_dark": "",
  "social_links": {"twitter":"","linkedin":"","github":"","instagram":""},
  "archive_layout": "grid",
  "archive_posts_per_page": 12,
  "archive_show_sidebar": false,
  "archive_show_category_filter": true,
  "single_layout": "standard",
  "single_show_progress_bar": true,
  "single_show_reading_time": true,
  "single_show_author_box": true,
  "single_show_social_share": true,
  "single_show_related_posts": true,
  "single_show_toc": false,
  "single_show_post_nav": true,
  "perf_remove_emoji": true,
  "perf_remove_embed": true,
  "perf_lazy_images": true,
  "perf_preload_fonts": true
}
```

### Color Schemes
sapphire (#0070F3), indigo (#635BFF), cobalt (#0C66E4), trust (#0530AD), commerce (#2a6e3f), signal (#F22F46), coral (#FF7A59), aubergine (#4A154B), midnight (#1e3a5f), stone (#57534e), carbon (#09090b), neutral (#18181b)

### Font Pairs
system (GitHub), inter (Linear/Notion), geist (Vercel), plex (IBM), dm-sans (Google), editorial (NYT), magazine (Premium), humanist (Publishing)

### Archive Layouts
grid, list, magazine, editorial, minimal

### Single Post Layouts
standard, hero-immersive, sidebar, editorial, cinematic, presentation, split, minimal-dark

## Deployment

Build once on dev machine:
```bash
cd wp-content/themes/brndle
./bin/release.sh 1.0.0
```
Upload `brndle-1.0.0.zip` to each site. No composer/npm needed on client sites.
