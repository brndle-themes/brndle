# brndle block catalog

**Generated from `blocks/*/block.json` on 2026-09-09. 20 blocks.**

Regenerate rather than hand-edit. The point of this file is that nobody has to open a
block.json to find out what a block accepts, and nobody guesses an attribute name.

## Rules that apply to all of them

1. **Blocks carry data, never hard-coded markup.** Every value a page shows is a block
   attribute, editable in the editor. A page built from raw HTML cannot be edited by
   the site owner and is a defect, not a shortcut.
2. **Check this catalog before adding a block.** Free and Pro both ship blocks; a new
   one that duplicates an existing block's job is dead weight.
3. **One layout family per page section.** Two sections sharing a block read as a template.
4. **`variant` is per-block but the page has ONE theme.** Do not mix light and dark
   sections on the same page.
5. Array attributes are plain PHP arrays. The item shapes below come from each block's
   own `example`, so they are the shapes the renderer actually reads.

## Quick index

| Block | Title | Full-width | Variations |
|---|---|---|---|
| `brndle/code` | Code Block | yes | - |
| `brndle/comparison-table` | Comparison Table | yes | - |
| `brndle/content-image-split` | Content + Image | yes | - |
| `brndle/cta` | CTA Section | yes | - |
| `brndle/faq` | FAQ Section | yes | - |
| `brndle/features` | Features Section | yes | - |
| `brndle/card-grid` | Card Grid | yes | - |
| `brndle/hero` | Hero Section | yes | hero-dark, hero-light, hero-gradient |
| `brndle/how-it-works` | How It Works | yes | - |
| `brndle/lead-form` | Lead Form | yes | - |
| `brndle/logos` | Logo Strip | yes | - |
| `brndle/post-feed` | Post Feed | yes | - |
| `brndle/pricing` | Pricing Section | yes | - |
| `brndle/pull-quote` | Pull Quote | yes | bordered-left, centered-large, outset |
| `brndle/stats` | Stats Section | yes | - |
| `brndle/tabs-accordion` | Tabs / Accordion | yes | - |
| `brndle/team` | Team | yes | - |
| `brndle/testimonials` | Testimonials Section | yes | - |
| `brndle/timeline` | Timeline | yes | - |
| `brndle/video-embed` | Video Embed | yes | - |

## Blocks

### `brndle/code` - Code Block

Syntax-highlighted code with copy-to-clipboard, line numbers, and an optional caption. Highlightjs is lazy-loaded only when a code block is on the page.

| Attribute | Type | Default |
|---|---|---|
| `code` | string | `""` |
| `language` | string | `"plain"` |
| `showLineNumbers` | boolean | `false` |
| `showCopy` | boolean | `true` |
| `caption` | string | `""` |
| `theme` | string | `"auto"` |
| `uniqueId` | string | `""` |

**Behaviour.** Has a view script for copy-to-clipboard. Pairs with the code font in the active pair.

### `brndle/comparison-table` - Comparison Table

Feature comparison grid with checkmark/x columns and optional highlighted plan column.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `columns` | array | `[]` |
| `rows` | array | `[]` |
| `highlight_column` | number | `-1` |
| `variant` | string | `"light"` |

Item shapes: `columns[]` = `{"label": "Starter", "sublabel": "$19"}`; `rows[]` = `{"feature": "Sites", "values": ["1", "10"]}`. A value is `true` (check), `false` (cross) or text, one per column. `highlight_column` is the 0-based index into `columns` (-1 for none). Older shapes (columns as strings or `{name, price}`, a blank first "corner" column, a `headers` list, rows keyed `label`) are upgraded at render by `AttributeMigrations`.

**Behaviour.** `columns` + `rows` + `highlight_column`. Good for us-vs-alternatives; do not use it to dump a spec sheet.

### `brndle/content-image-split` - Content + Image

Side-by-side content and image section with flexible layout options.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `description` | string | `""` |
| `bullets` | array | `[]` |
| `image` | string | `""` |
| `image_id` | number | `0` |
| `image_alt` | string | `""` |
| `image_position` | string | `"right"` |
| `cta_text` | string | `""` |
| `cta_url` | string | `"#"` |
| `variant` | string | `"light"` |

Item shapes: `bullets[]` = `"Lighthouse 100 by default"`

**Behaviour.** `image_position` left/right. With no `image` it renders a visible 'Add an image URL' placeholder box, so never ship it empty. `bullets` is an array of plain strings.

### `brndle/cta` - CTA Section

Call-to-action section with headline, subtitle, and buttons.

| Attribute | Type | Default |
|---|---|---|
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `cta_primary` | string | `""` |
| `cta_primary_url` | string | `"#"` |
| `cta_secondary` | string | `""` |
| `cta_secondary_url` | string | `"#"` |
| `variant` | string | `"dark"` |

**Behaviour.** Defaults to the dark variant. On a light-locked page set variant light or it breaks the page theme.

### `brndle/faq` - FAQ Section

Frequently asked questions with CSS-only accordion.

| Attribute | Type | Default |
|---|---|---|
| `title` | string | `"Frequently asked questions"` |
| `items` | array | `[]` |

Item shapes: `items[]` = `{"question": "Do I need a page builder?", "answer": "No. Brndle ships native Gutenberg blocks rendered server-side."}`

**Behaviour.** Emits FAQPage JSON-LD automatically from items, so do not hand-add schema alongside it. Items need both question and answer or they are filtered out.

### `brndle/features` - Features Section

Alternating feature rows with image, title, description, and bullet points.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `features` | array | `[]` |
| `variant` | string | `"light"` |

Item shapes: `features[]` = `{"title": "Server-rendered blocks", "description": "Lighter pages, better SEO, faster TTFB."}`

**Behaviour.** SPOTLIGHT, not a card grid. Each feature renders full-width `max-w-3xl` UNLESS it has an `image`, in which case it becomes an alternating two-column row. Four image-less features render as a tall sparse column. Always supply images.

### `brndle/hero` - Hero Section

Full-width hero section with eyebrow, headline, subtitle, CTAs, image, and logo strip.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `cta_primary` | string | `""` |
| `cta_primary_url` | string | `"#"` |
| `cta_secondary` | string | `""` |
| `cta_secondary_url` | string | `"#"` |
| `image` | string | `""` |
| `image_id` | number | `0` |
| `image_alt` | string | `""` |
| `variant` | string | `"dark"` |
| `logos` | array | `[]` |

**Behaviour.** Variants dark / light / gradient (3 registered variations). `logos` lets a trust strip sit inside the hero; the house rule is to keep it OUT and use the logos block below instead.

### `brndle/how-it-works` - How It Works

Numbered process steps with optional icons and descriptions.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `steps` | array | `[]` |
| `layout` | string | `"horizontal"` |
| `variant` | string | `"light"` |

Item shapes: `steps[]` = `{"title": "Install", "description": "One-click install from your dashboard."}`

**Behaviour.** `layout` horizontal / vertical. Steps carry title + description only. Label steps with the verb, never 'Step 1 / Stage 1'.

### `brndle/lead-form` - Lead Form

Email capture section with headline, description, and customizable form fields.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `fields` | array | `[{"label": "Email", "type": "email", "required` |
| `button_text` | string | `"Get Started"` |
| `success_message` | string | `"Thanks! We'll be in touch."` |
| `form_action` | string | `""` |
| `mailchimp_list_id` | string | `""` |
| `layout` | string | `"stacked"` |
| `variant` | string | `"light"` |

Item shapes: `fields[]` = `{"label": "Email", "type": "email", "required": true, "placeholder": "you@company.com"}`

**Behaviour.** Posts to `POST /forms/submit` and stores in `wp_brndle_submissions`. Guards already present: nonce, honeypot, 5/min/IP rate limit, sanitisation. Setting `form_action` sends the post elsewhere and DISABLES the built-in handler. `mailchimp_list_id` needs an API key in settings.

### `brndle/logos` - Logo Strip

Trust bar showing client/partner logos.

| Attribute | Type | Default |
|---|---|---|
| `title` | string | `"Trusted by industry leaders"` |
| `companies` | array | `[]` |
| `variant` | string | `"light"` |

Item shapes: `companies[]` = `"Acme"`

**Behaviour.** `companies` accepts EITHER plain strings (rendered as text wordmarks) OR objects `{url, alt, name}` for real image marks. Prefer objects; text wordmarks read as a placeholder.

### `brndle/post-feed` - Post Feed

A category-curated row of posts, using the homepage section styles.

| Attribute | Type | Default |
|---|---|---|
| `categoryId` | number | `0` |
| `style` | string | `"grid-3col"` |
| `count` | number | `3` |
| `showTitle` | boolean | `true` |
| `showViewAll` | boolean | `true` |
| `heading` | string | `""` |

**Behaviour.** NEW in 2.3.0. Wraps the seven homepage section styles so a category rail works inside any page, not only on the blog-as-front-page path. Empty categories render nothing rather than an empty heading. Thumbnails are cache-primed in one query to avoid N+1.

### `brndle/pricing` - Pricing Section

Pricing table with 2-3 plans, featured plan highlight, and feature lists.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `plans` | array | `[]` |
| `variant` | string | `"light"` |

Item shapes: `plans[]` = `{"name": "Starter", "price": "$19", "period": "/mo", "features": ["1 site", "Email support"], "cta_text": "Start free", "featured": false}`

**Behaviour.** Plan keys the renderer actually reads: name, price, period, description, features[], cta_text, cta_url, featured, badge, original_price, billing_group. `featured: true` lifts one column.

### `brndle/pull-quote` - Pull Quote

Editorial pull quote with three variants: bordered-left, centered-large, and outset.

| Attribute | Type | Default |
|---|---|---|
| `quote` | string | `""` |
| `cite` | string | `""` |
| `citeUrl` | string | `""` |
| `variant` | string | `"bordered-left"` |
| `accentColor` | string | `"accent"` |
| `uniqueId` | string | `""` |

**Behaviour.** Single editorial quote. One per page at most.

### `brndle/stats` - Stats Section

Key metrics and statistics in a row.

| Attribute | Type | Default |
|---|---|---|
| `items` | array | `[]` |
| `variant` | string | `"light"` |

Item shapes: `items[]` = `{"value": "98%", "label": "Customer satisfaction"}`

**Behaviour.** Numbers only. Never populate with estimated figures; the block gives them the authority of measurement.

### `brndle/tabs-accordion` - Tabs / Accordion

One block, two display modes - tabs or accordion - sharing the same data. Full WAI-ARIA tabs and disclosure behaviour.

| Attribute | Type | Default |
|---|---|---|
| `title` | string | `""` |
| `items` | array | `[]` |
| `displayMode` | string | `"tabs"` |
| `tabsAlignment` | string | `"start"` |
| `accordionMode` | string | `"single"` |
| `accordionDefault` | string | `"closed"` |
| `uniqueId` | string | `""` |

Item shapes: `items[]` = `{"label": "Plan", "content": "Sketch the milestones, agree the constraints, write them down."}`

**Behaviour.** Has a view script. Hides content behind interaction, so never put the primary sales argument in it.

### `brndle/team` - Team

Team member cards with photo, name, role, bio, and optional social links.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `members` | array | `[]` |
| `columns` | string | `"3"` |
| `variant` | string | `"light"` |

Item shapes: `members[]` = `{"name": "Alex Reyes", "role": "Founder & CEO"}`

**Behaviour.** Grid of people. Needs real photos.

### `brndle/testimonials` - Testimonials Section

Customer testimonials in a card grid layout.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `items` | array | `[]` |

Item shapes: `items[]` = `{"quote": "We replaced our page builder and saw 40% faster pages overnight.", "name": "Alex Reyes", "role": "Head of Growth", "company": "Acme"}`

**Behaviour.** Quote body should stay under about three lines. Attribution needs name plus role plus company.

### `brndle/timeline` - Timeline

Vertical list of milestones with date, title, description, and an optional icon.

| Attribute | Type | Default |
|---|---|---|
| `title` | string | `""` |
| `items` | array | `[]` |
| `iconStyle` | string | `"dot"` |
| `connector` | string | `"solid"` |
| `density` | string | `"comfortable"` |
| `uniqueId` | string | `""` |

Item shapes: `items[]` = `{"date": "March 2025", "title": "Beta launch", "description": "First closed-beta cohort onboarded across three pilot sites."}`

**Behaviour.** Has a view script, enqueued lazily only when items exist.

### `brndle/video-embed` - Video Embed

Section wrapper for YouTube, Vimeo, or self-hosted video with optional headline.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `video_url` | string | `""` |
| `video_type` | string | `"youtube"` |
| `poster` | string | `""` |
| `autoplay` | boolean | `false` |
| `show_controls` | boolean | `true` |
| `aspect_ratio` | string | `"16/9"` |
| `max_width` | string | `"full"` |
| `variant` | string | `"dark"` |

**Behaviour.** Facade pattern. Set geometry inline if you script the iframe; scoped CSS will not reach a runtime-created element.


### `brndle/card-grid` - Card Grid

A compact grid of linked cards. For short items that do not each deserve a full-width spotlight.

| Attribute | Type | Default |
|---|---|---|
| `eyebrow` | string | `""` |
| `title` | string | `""` |
| `subtitle` | string | `""` |
| `columns` | number | `2` |
| `featureFirst` | boolean | `false` |
| `items` | array | `[]` |
| `variant` | string | `"light"` |

Item shapes: `items[]` = `{"title":"...","description":"...","link_text":"...","link_url":"..."}`

**Behaviour.** NEW in 2.3.0. Use this, not Features, for short parallel items such as
service lanes. Features is a spotlight: it gives each item a full-width alternating row
and needs a per-item image, so four short items become roughly 2,800px of near-empty
cards. `featureFirst` promotes card one across the row to give the grid a focal point -
but only turn it on when the remaining cards still fill their rows. With 4 items and 2
columns it leaves an orphan on the last row, so 4 items want `featureFirst: false`.
