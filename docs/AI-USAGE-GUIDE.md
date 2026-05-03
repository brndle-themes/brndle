# Brndle — AI Usage Guide

**Audience:** AI agents (Claude, GPT, Codex, others) and humans scripting WordPress content. The guide is self-contained — read this once and you can publish blogs, build landing pages, and configure Brndle sites without opening the WordPress block editor.

**Theme version this guide targets:** Brndle 2.1.0 (18 blocks).

---

## How content is stored

Brndle pages and posts are standard WordPress content — block markup is stored in `wp_posts.post_content` as HTML comments wrapping each block's serialized JSON attributes. Example:

```html
<!-- wp:brndle/hero {"title":"Build faster","variant":"dark"} /-->
```

Three creation paths are equally valid; pick whichever fits the environment:

1. **WP-CLI** — write the block markup to a file and use `wp post create file.html --post_type=post --post_status=publish`.
2. **WordPress REST API** — `POST /wp-json/wp/v2/posts` (or `pages`) with `{ "content": "<!-- wp:... --> ...", "status": "publish" }`. Requires authentication.
3. **PHP / direct DB** — `wp_insert_post([ 'post_content' => '<!-- wp:... -->', 'post_status' => 'publish' ])` inside any theme hook or WP-CLI custom command.

There is **no separate API for blocks**. The block markup IS the content.

---

## The 18-block catalog at a glance

Pick the right register for the page:

### Marketing / landing-page register (14 blocks)

Use these for product pages, sales pages, and homepages. Full-width sections that fill the article column edge-to-edge.

| Block | Purpose |
|---|---|
| `brndle/hero` | Above-the-fold hero with title, subtitle, CTA buttons, optional image |
| `brndle/stats` | Key metrics row ("100 sites", "0 KB JS", etc.) |
| `brndle/features` | Alternating feature sections — text + image, image + text |
| `brndle/testimonials` | Customer testimonial cards with avatar + quote + role |
| `brndle/pricing` | Pricing table with multiple plans, featured plan highlighted |
| `brndle/cta` | Call-to-action banner — title + subtitle + 1–2 CTA buttons |
| `brndle/faq` | Accordion FAQ section. **Emits `FAQPage` JSON-LD schema for Google rich results.** |
| `brndle/logos` | Trust logos / client strip |
| `brndle/content-image-split` | One feature at a time — content on one side, image on the other |
| `brndle/how-it-works` | Numbered steps — process / onboarding |
| `brndle/lead-form` | Inline lead capture with name + email + (optional) message |
| `brndle/comparison-table` | Free vs Pro vs Enterprise feature comparison |
| `brndle/team` | Team member cards with avatar + name + role + bio |
| `brndle/video-embed` | YouTube / Vimeo / Loom embed with poster image |

### Editorial register (4 blocks, v2.1+)

Use these **inside long-form posts and articles**, mixed with `core/paragraph`, `core/heading`, `core/list`, `core/image`. Don't combine with marketing blocks in the same content well — pick one register per page.

| Block | Purpose |
|---|---|
| `brndle/code` | Syntax-highlighted code block with copy button + line numbers + caption |
| `brndle/pull-quote` | Editorial pull quote, 3 variants (bordered-left, centered-large, outset) |
| `brndle/timeline` | Vertical milestones list with date + title + description |
| `brndle/tabs-accordion` | Combined block — displayMode toggle picks tab vs accordion presentation |

---

## Block reference — full attributes

For every block, the JSON below is a complete inserter-equivalent snapshot. Omit any attribute to use its default.

### Marketing blocks

#### `brndle/hero` — Hero section

```html
<!-- wp:brndle/hero {
  "eyebrow": "AI-POWERED",
  "title": "Build landing pages in minutes",
  "subtitle": "One theme, unlimited client sites. Zero page builders.",
  "cta_primary": "Start free",
  "cta_primary_url": "/signup",
  "cta_secondary": "Watch demo",
  "cta_secondary_url": "/demo",
  "image": "https://example.com/hero.png",
  "variant": "dark",
  "logos": ["Stripe", "Vercel", "Linear", "Figma"]
} /-->
```

- `variant`: `dark` | `light` | `gradient`
- `logos`: array of strings (renders as text) OR objects `{"url":"img.png","name":"Co"}` (renders as images)
- Empty `title` → block doesn't render

#### `brndle/stats` — Metrics row

```html
<!-- wp:brndle/stats {
  "items": [
    {"value": "100", "label": "Lighthouse Score"},
    {"value": "0 KB", "label": "JavaScript"},
    {"value": "<1s", "label": "TTFB"}
  ],
  "variant": "light"
} /-->
```

- `variant`: `dark` | `light`

#### `brndle/features` — Alternating feature sections

```html
<!-- wp:brndle/features {
  "eyebrow": "Features",
  "title": "Everything you need",
  "subtitle": "Server-rendered. Lighthouse 100. Zero bloat.",
  "features": [
    {
      "title": "Server-side blocks",
      "description": "Every block renders in PHP. No client-side hydration overhead.",
      "bullets": ["No JS for static content", "Cacheable by every WordPress cache plugin"],
      "image": "https://example.com/ssr.png",
      "icon": "zap"
    }
  ],
  "variant": "light"
} /-->
```

- `features[].icon`: Lucide icon name (kebab-case). Must be in the curated set — see `bin/copy-lucide-icons.mjs`.
- Features alternate left/right automatically based on index.

#### `brndle/testimonials` — Customer testimonials

```html
<!-- wp:brndle/testimonials {
  "eyebrow": "Testimonials",
  "title": "Trusted by teams that ship",
  "items": [
    {
      "quote": "Brndle replaced three plugins on our agency stack.",
      "name": "Jane Doe",
      "role": "CTO at AcmeCo",
      "avatar": "https://example.com/jane.jpg",
      "stars": 5
    }
  ]
} /-->
```

#### `brndle/pricing` — Pricing table

```html
<!-- wp:brndle/pricing {
  "eyebrow": "Pricing",
  "title": "Simple plans",
  "subtitle": "Pay annually, save 20%.",
  "plans": [
    {
      "name": "Starter",
      "description": "For solo developers.",
      "price": "$19",
      "period": "/mo",
      "features": ["1 site", "Email support"],
      "cta_text": "Start free",
      "cta_url": "/signup",
      "featured": false,
      "badge": ""
    },
    {
      "name": "Pro",
      "description": "For teams and agencies.",
      "price": "$49",
      "period": "/mo",
      "features": ["Unlimited sites", "Priority support", "AI page generator"],
      "cta_text": "Go Pro",
      "cta_url": "/pro",
      "featured": true,
      "badge": "MOST POPULAR"
    }
  ]
} /-->
```

#### `brndle/cta` — Call-to-action banner

```html
<!-- wp:brndle/cta {
  "title": "Ready to ship?",
  "subtitle": "Join 1,200 teams already using Brndle.",
  "cta_primary": "Start free",
  "cta_primary_url": "/signup",
  "cta_secondary": "Talk to sales",
  "cta_secondary_url": "/contact",
  "variant": "dark"
} /-->
```

- `variant`: `dark` | `light` | `gradient`

#### `brndle/faq` — FAQ accordion (with FAQPage schema)

```html
<!-- wp:brndle/faq {
  "title": "Frequently asked questions",
  "items": [
    {
      "question": "Do I need a page builder?",
      "answer": "No. Brndle ships native Gutenberg blocks rendered server-side."
    },
    {
      "question": "Will it work with my caching plugin?",
      "answer": "Yes. Brndle is fully compatible with WP Rocket, LiteSpeed, and others."
    }
  ]
} /-->
```

- **Emits `FAQPage` JSON-LD** for Google rich results. Use this when you want SEO benefit. For plain disclosure UI without schema, use `brndle/tabs-accordion` in accordion mode.

#### `brndle/logos` — Trust logo strip

```html
<!-- wp:brndle/logos {
  "title": "Trusted by",
  "companies": ["Stripe", "Vercel", "Linear", "Figma", "GitHub"],
  "variant": "light"
} /-->
```

#### `brndle/content-image-split` — Single feature side-by-side

```html
<!-- wp:brndle/content-image-split {
  "eyebrow": "How it works",
  "title": "AI generates the markup",
  "description": "Describe the page; Brndle outputs the block JSON.",
  "image": "https://example.com/ai-flow.png",
  "imagePosition": "right",
  "ctaText": "Try it",
  "ctaUrl": "/demo"
} /-->
```

- `imagePosition`: `left` | `right`

#### `brndle/how-it-works` — Numbered steps

```html
<!-- wp:brndle/how-it-works {
  "eyebrow": "Process",
  "title": "Three steps to ship",
  "items": [
    {"title": "Plan", "description": "Sketch the page in plain English."},
    {"title": "Generate", "description": "AI writes the block markup."},
    {"title": "Publish", "description": "Save and you're live."}
  ]
} /-->
```

#### `brndle/lead-form` — Inline lead capture

```html
<!-- wp:brndle/lead-form {
  "title": "Stay in the loop",
  "subtitle": "Get launch updates.",
  "submit_text": "Subscribe",
  "form_action": "",
  "show_message_field": false,
  "success_message": "Thanks — check your inbox."
} /-->
```

- Empty `form_action` (default) routes submission through Brndle's REST endpoint (`brndle/v1/forms`). Provide a URL to override.

#### `brndle/comparison-table` — Plan comparison grid

```html
<!-- wp:brndle/comparison-table {
  "title": "Compare plans",
  "headers": ["Feature", "Free", "Pro", "Enterprise"],
  "rows": [
    {"feature": "Sites", "values": ["1", "Unlimited", "Unlimited"]},
    {"feature": "AI page generator", "values": ["—", "✓", "✓"]},
    {"feature": "Priority support", "values": ["—", "—", "✓"]}
  ]
} /-->
```

#### `brndle/team` — Team member cards

```html
<!-- wp:brndle/team {
  "eyebrow": "Team",
  "title": "The people behind Brndle",
  "members": [
    {
      "name": "Varun Dubey",
      "role": "Lead engineer",
      "avatar": "https://example.com/varun.jpg",
      "bio": "Building Brndle since the beginning.",
      "twitter": "https://x.com/varundubey",
      "linkedin": "https://linkedin.com/in/varundubey"
    }
  ]
} /-->
```

#### `brndle/video-embed` — Embed with poster

```html
<!-- wp:brndle/video-embed {
  "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
  "poster": "https://example.com/poster.jpg",
  "caption": "Watch the 90-second demo."
} /-->
```

---

### Editorial blocks (v2.1+)

#### `brndle/code` — Syntax-highlighted code

```html
<!-- wp:brndle/code {
  "language": "js",
  "code": "const greeting = 'hello';\nconsole.log(greeting);",
  "showLineNumbers": true,
  "showCopy": true,
  "theme": "auto",
  "caption": "Source: app/utils.js"
} /-->
```

- `language` (required for highlighting): `plain` (no highlighting) | `bash` | `css` | `diff` | `dockerfile` | `html` | `js` | `json` | `jsx` | `markdown` | `nginx` | `php` | `python` | `scss` | `sql` | `ts` | `tsx` | `yaml`
- `showLineNumbers`: server-side rendered line numbers in a left rail
- `showCopy`: copy-to-clipboard button bottom-right (visible on hover / focus-within)
- `theme`: `auto` (default — follows page dark mode) | `light` | `dark`
- `caption`: optional one-line attribution under the code (file path / commit / source URL). Limited HTML allowed.
- **Empty `code` → block doesn't render.**
- Highlight.js is loaded **lazily** as an ES module from CDN ONLY when a code block approaches the viewport. Pages without code don't pay the JS cost.
- **Print:** copy button hidden, line numbers hidden, monospace + pre-wrap preserved.

#### `brndle/pull-quote` — Editorial pull quote

```html
<!-- wp:brndle/pull-quote {
  "variant": "bordered-left",
  "quote": "The best designs are the ones you don't notice.",
  "cite": "Brndle design notes",
  "citeUrl": "https://example.com/notes",
  "accentColor": "accent"
} /-->
```

- `variant`: `bordered-left` (default — accent rule on left, italic 1.5rem) | `centered-large` (decorative open-quote glyph above, 2rem display weight) | `outset` (breaks out of the article column ±80px at lg+)
- `accentColor`: `accent` (default) | `text-primary` | `text-tertiary`
- `cite` + `citeUrl` are both optional. `citeUrl` renders `rel="nofollow noopener"`.
- **Empty `quote` → block doesn't render.**
- **Pick the variant:** `bordered-left` for in-flow editorial; `centered-large` for full-width emphasis (use sparingly); `outset` for long-form features.

#### `brndle/timeline` — Vertical milestones

```html
<!-- wp:brndle/timeline {
  "title": "Release history",
  "iconStyle": "numbered",
  "connector": "solid",
  "density": "comfortable",
  "items": [
    {
      "date": "March 2025",
      "title": "Beta",
      "description": "First closed-beta cohort onboarded across three pilot sites."
    },
    {
      "date": "May 2025",
      "title": "Public 1.0",
      "description": "Open release with 14 server-rendered blocks.",
      "icon": "rocket"
    }
  ]
} /-->
```

- `iconStyle`: `dot` (small accent circle) | `numbered` (`01`, `02`, `03` …) | `lucide` (per-item icon — only used when this is set)
- `connector`: `solid` (default) | `dashed` | `none`
- `density`: `comfortable` (default) | `compact` (mobile <640px is forced compact regardless)
- `items[].icon`: Lucide name. Only used when `iconStyle === "lucide"`. Must be in the curated set.
- Reveal animation: items fade-in + slide-from-left as they enter the viewport. Disabled under `prefers-reduced-motion`.
- **Empty `items` → block doesn't render.**

#### `brndle/tabs-accordion` — Tabs OR accordion (combined)

**Tabs mode:**

```html
<!-- wp:brndle/tabs-accordion {
  "displayMode": "tabs",
  "tabsAlignment": "start",
  "title": "How it works",
  "items": [
    {"label": "Plan", "content": "Sketch the milestones."},
    {"label": "Build", "content": "Ship phase-by-phase."},
    {"label": "Verify", "content": "Browser-check at desktop and mobile."}
  ]
} /-->
```

**Accordion mode:**

```html
<!-- wp:brndle/tabs-accordion {
  "displayMode": "accordion",
  "accordionMode": "single",
  "accordionDefault": "first",
  "title": "Common questions",
  "items": [
    {"label": "Does this emit FAQPage schema?", "content": "No — use <code>brndle/faq</code> if you want FAQ schema."},
    {"label": "Can I open multiple at once?", "content": "Switch <strong>accordionMode</strong> to <code>multiple</code>."}
  ]
} /-->
```

- `displayMode`: `tabs` (default) | `accordion`
- `tabsAlignment` (tabs only): `start` (default) | `center` | `end`
- `accordionMode` (accordion only): `single` (radio-like — opening one closes others) | `multiple` (any number open)
- `accordionDefault` (accordion only): `closed` (default) | `first` | `all` (only meaningful in `multiple` mode)
- `items[].content`: Limited HTML — `<strong>`, `<em>`, `<a>`, `<br>`, `<code>`. Double-newlines become `<p>` via `wpautop`.
- Mobile (<640px): tabs strip becomes horizontally scrollable with snap.
- **Never emits `FAQPage` JSON-LD** — that's `brndle/faq`'s exclusive job.
- **Empty `items` → block doesn't render.**

---

## When to pick which block

### "I want to break up a long article"

| Need | Block |
|---|---|
| Highlight a key sentence | `brndle/pull-quote` (`bordered-left` for inline, `centered-large` for emphasis) |
| Show a code sample | `brndle/code` |
| List milestones / steps in order | `brndle/timeline` |
| Group related content into switchable panels | `brndle/tabs-accordion` (tabs mode) |
| Group expandable detail sections | `brndle/tabs-accordion` (accordion mode, single) |
| Group SEO-tagged FAQ questions | `brndle/faq` (emits FAQPage schema) |

### "I want to build a landing page"

| Section | Block |
|---|---|
| Above-the-fold hero | `brndle/hero` |
| Trust strip (logos) | `brndle/logos` |
| Key metrics | `brndle/stats` |
| Feature breakdown | `brndle/features` (multiple) or `brndle/content-image-split` (per-feature) |
| How it works | `brndle/how-it-works` |
| Pricing | `brndle/pricing` |
| Testimonials | `brndle/testimonials` |
| Plan comparison | `brndle/comparison-table` |
| Team | `brndle/team` |
| Video walkthrough | `brndle/video-embed` |
| Frequently asked | `brndle/faq` |
| Lead capture | `brndle/lead-form` |
| Bottom CTA | `brndle/cta` |

### "I want a blog post about a code-heavy topic"

Use the editorial register exclusively:

```
core/heading              ← title
core/paragraph            ← intro
brndle/pull-quote         ← key insight
core/heading              ← section title
core/paragraph            ← section body
brndle/code               ← code sample
core/paragraph            ← explanation
brndle/timeline           ← if there's a step-by-step
core/paragraph            ← conclusion
```

Don't drop a `brndle/hero` or `brndle/pricing` into a blog post — that's the marketing register. Mixing registers creates a "two themes glued together" feel.

---

## Page templates

Brndle has 4 page templates. Set via `update_post_meta($id, '_wp_page_template', 'template-{slug}.php')`.

| Template | Slug | Use case |
|---|---|---|
| **Default** | (none) | Standard page — header + prose content + footer. Best for blog posts, About, Contact, Privacy. |
| **Landing Page** | `template-landing.php` | Full-width block sections with header + footer. **Use for marketing landing pages.** |
| **Full Canvas** | `template-canvas.php` | Zero chrome — no header, no footer, no padding. Pure content. Use for custom creative pages, app-like experiences. |
| **Transparent Header** | `template-transparent.php` | Header floats over content with transparent bg, turns solid on scroll. Best for pages with dark hero sections. |

---

## Site Configuration (REST API)

Brndle exposes a REST API at `/wp-json/brndle/v1/settings`. Authentication is required — application passwords or admin cookies work.

### Endpoints

| Method | Path | Purpose |
|---|---|---|
| `GET` | `/wp-json/brndle/v1/settings` | Read all settings |
| `POST` | `/wp-json/brndle/v1/settings` | Save (deep-merged into existing) |
| `DELETE` | `/wp-json/brndle/v1/settings` | Reset to defaults |
| `GET` | `/wp-json/brndle/v1/settings/export` | Export JSON snapshot |
| `POST` | `/wp-json/brndle/v1/settings/import` | Import JSON snapshot |

### Common settings

```json
{
  "color_scheme": "sapphire",
  "custom_accent": "",
  "font_pair": "inter",
  "font_size_base": 16,
  "header_style": "sticky",
  "header_sticky_mode": "sticky-fixed",
  "header_search_enabled": false,
  "header_cta_text": "Get Started",
  "header_cta_url": "/signup",
  "footer_style": "dark",
  "footer_columns": 3,
  "dark_mode_default": "light",
  "dark_mode_toggle": true,
  "single_layout": "standard",
  "single_show_updated_date": true,
  "perf_view_transitions": true,
  "perf_critical_css": true
}
```

### Choices

- **`color_scheme`** (12): `sapphire`, `indigo`, `cobalt`, `trust`, `commerce`, `signal`, `coral`, `aubergine`, `midnight`, `stone`, `carbon`, `neutral`
- **`font_pair`** (8): `system`, `inter`, `geist`, `plex`, `dm-sans`, `editorial`, `magazine`, `humanist`
- **`header_style`** (8): `sticky`, `solid`, `transparent`, `centered`, `minimal`, `split`, `banner`, `glass`
- **`header_sticky_mode`** (4): `static`, `sticky-fixed`, `sticky-fade`, `sticky-hide-on-scroll`
- **`footer_style`** (6): `dark`, `light`, `columns`, `minimal`, `big`, `stacked`
- **`single_layout`** (8): `standard`, `hero-immersive`, `sidebar`, `editorial`, `cinematic`, `presentation`, `split`, `minimal-dark`
- **`dark_mode_default`** (3): `light`, `dark`, `system`

### Curl example

```bash
curl -X POST 'https://example.com/wp-json/brndle/v1/settings' \
  -u 'username:application-password' \
  -H 'Content-Type: application/json' \
  -d '{"color_scheme":"midnight","header_style":"glass","footer_style":"big"}'
```

---

## Complete worked examples

### Example 1 — SaaS landing page (template-landing)

Save as `landing.html`, then:

```bash
wp post create landing.html \
  --post_type=page \
  --post_title="Brndle for Agencies" \
  --post_status=publish \
  --meta_input='{"_wp_page_template":"template-landing.php"}'
```

`landing.html`:

```html
<!-- wp:brndle/hero {
  "eyebrow": "FOR AGENCIES",
  "title": "Ship client sites in hours, not weeks",
  "subtitle": "One theme. Unlimited sites. Native Gutenberg.",
  "cta_primary": "Start free",
  "cta_primary_url": "/signup",
  "cta_secondary": "Watch 90s demo",
  "cta_secondary_url": "/demo",
  "variant": "dark",
  "logos": ["Stripe", "Vercel", "Linear"]
} /-->

<!-- wp:brndle/stats {
  "items": [
    {"value": "100", "label": "Lighthouse Score"},
    {"value": "0 KB", "label": "JavaScript"},
    {"value": "<1s", "label": "TTFB"},
    {"value": "18", "label": "Native blocks"}
  ],
  "variant": "light"
} /-->

<!-- wp:brndle/features {
  "eyebrow": "What you get",
  "title": "Built for agency speed",
  "subtitle": "Server-rendered blocks. Zero page builders. Lighthouse 100 baseline.",
  "features": [
    {
      "title": "Server-side blocks",
      "description": "Every block renders in PHP. No client-side hydration overhead. Caches cleanly with WP Rocket, LiteSpeed, or any object cache.",
      "bullets": ["Zero JS for static content", "Object-cache friendly"],
      "image": "https://example.com/ssr.png",
      "icon": "zap"
    },
    {
      "title": "AI page generation",
      "description": "Describe the page in plain English. Brndle generates valid block markup. Drop-in or refine in the editor.",
      "bullets": ["Works with Claude, GPT, others", "Generates valid block JSON"],
      "image": "https://example.com/ai.png",
      "icon": "sparkles"
    }
  ],
  "variant": "light"
} /-->

<!-- wp:brndle/how-it-works {
  "eyebrow": "Workflow",
  "title": "Three steps to ship",
  "items": [
    {"title": "Pick a layout", "description": "8 single-post layouts, 8 header styles, 6 footer styles, 12 color schemes."},
    {"title": "Drop in blocks", "description": "Hero, stats, features, pricing — all server-rendered."},
    {"title": "Publish", "description": "No build step required for end users."}
  ]
} /-->

<!-- wp:brndle/pricing {
  "eyebrow": "Pricing",
  "title": "Simple plans",
  "subtitle": "Pay annually, save 20%.",
  "plans": [
    {
      "name": "Solo",
      "description": "For freelance developers.",
      "price": "$19",
      "period": "/mo",
      "features": ["1 site", "Email support", "Basic blocks"],
      "cta_text": "Start free",
      "cta_url": "/signup",
      "featured": false,
      "badge": ""
    },
    {
      "name": "Agency",
      "description": "For teams shipping client work.",
      "price": "$49",
      "period": "/mo",
      "features": ["Unlimited sites", "Priority support", "AI page generator", "All blocks"],
      "cta_text": "Go Agency",
      "cta_url": "/signup?plan=agency",
      "featured": true,
      "badge": "MOST POPULAR"
    }
  ]
} /-->

<!-- wp:brndle/testimonials {
  "eyebrow": "Customers",
  "title": "Trusted by teams that ship",
  "items": [
    {
      "quote": "Brndle replaced three plugins on our agency stack. Lighthouse went from 67 to 100.",
      "name": "Jane Doe",
      "role": "CTO at AcmeCo",
      "avatar": "https://example.com/jane.jpg",
      "stars": 5
    }
  ]
} /-->

<!-- wp:brndle/faq {
  "title": "Frequently asked questions",
  "items": [
    {"question": "Do I need a page builder?", "answer": "No. Brndle ships native Gutenberg blocks rendered server-side."},
    {"question": "Will it work with my caching plugin?", "answer": "Yes. Brndle is fully compatible with WP Rocket, LiteSpeed, and others."},
    {"question": "Can I customize the colors?", "answer": "Yes. 12 preset schemes plus a custom accent color picker."}
  ]
} /-->

<!-- wp:brndle/cta {
  "title": "Ready to ship?",
  "subtitle": "Join 1,200 teams already using Brndle.",
  "cta_primary": "Start free",
  "cta_primary_url": "/signup",
  "cta_secondary": "Talk to sales",
  "cta_secondary_url": "/contact",
  "variant": "dark"
} /-->
```

### Example 2 — Technical blog post (default template)

Save as `post.html`, then:

```bash
wp post create post.html \
  --post_type=post \
  --post_title="How Brndle lazy-loads highlight.js" \
  --post_status=publish \
  --post_category=$(wp term list category --name="WordPress" --field=term_id)
```

`post.html`:

```html
<!-- wp:paragraph -->
<p>Brndle 2.1 added a syntax-highlighted code block. The constraint was clear: <strong>zero JavaScript cost on pages that don't have code blocks</strong>. Here's how the implementation actually works.</p>
<!-- /wp:paragraph -->

<!-- wp:brndle/pull-quote {
  "variant": "bordered-left",
  "quote": "The best build is the one users never know happened.",
  "cite": "Brndle design notes",
  "accentColor": "accent"
} /-->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">The lazy-load pattern</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Highlight.js is loaded as an ES module from a CDN — but only when a <code>.brndle-code</code> element approaches the viewport. The trigger is an IntersectionObserver with a 200px rootMargin so the highlighter has time to load before the user actually sees the code.</p>
<!-- /wp:paragraph -->

<!-- wp:brndle/code {
  "language": "js",
  "code": "let hljsPromise = null;\n\nfunction loadHljs(cdn) {\n  if (hljsPromise) return hljsPromise;\n  hljsPromise = import(/* webpackIgnore: true */ cdn)\n    .then((mod) => mod.default || mod);\n  return hljsPromise;\n}\n\nconst io = new IntersectionObserver((entries) => {\n  entries.forEach((entry) => {\n    if (!entry.isIntersecting) return;\n    io.unobserve(entry.target);\n    loadHljs(CDN).then((hljs) => hljs.highlightElement(entry.target));\n  });\n}, { rootMargin: '200px 0px' });",
  "showLineNumbers": true,
  "showCopy": true,
  "caption": "Source: blocks/src/code-view.js"
} /-->

<!-- wp:paragraph -->
<p>Three things make this premium-tier:</p>
<!-- /wp:paragraph -->

<!-- wp:brndle/timeline {
  "iconStyle": "numbered",
  "connector": "solid",
  "items": [
    {
      "date": "Step 1",
      "title": "The promise is cached",
      "description": "Calling loadHljs() multiple times returns the same in-flight promise — never two parallel imports for the same CDN URL."
    },
    {
      "date": "Step 2",
      "title": "Pages without code never load it",
      "description": "If init() finds zero .brndle-code elements, the IntersectionObserver is never created and the dynamic import never fires."
    },
    {
      "date": "Step 3",
      "title": "Webpack stays out of the way",
      "description": "The /* webpackIgnore: true */ comment tells webpack not to follow the dynamic import. The CDN URL is resolved at runtime, not bundled."
    }
  ]
} /-->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Common questions</h2>
<!-- /wp:heading -->

<!-- wp:brndle/tabs-accordion {
  "displayMode": "accordion",
  "accordionMode": "single",
  "accordionDefault": "first",
  "items": [
    {
      "label": "What if the CDN is blocked?",
      "content": "The <code>brndle/code_highlighter_strategy</code> filter switches to a bundled subset (<code>inline</code>) or a server-side highlighter (<code>php</code>). The code stays readable as plain monospaced text either way."
    },
    {
      "label": "Why not bundle highlight.js outright?",
      "content": "Highlight.js with all 18 languages is ~150KB compressed. CDN-loading on demand keeps the initial page weight at zero for non-code pages."
    },
    {
      "label": "What about line numbers?",
      "content": "They're rendered server-side in Blade as a sibling <code>&lt;aside aria-hidden&gt;</code>. The JS controller never touches innerHTML — keeps the surface free of XSS-shape patterns."
    }
  ]
} /-->

<!-- wp:paragraph -->
<p>The full implementation is in <a href="https://github.com/brndle-themes/brndle/blob/main/blocks/src/code-view.js">blocks/src/code-view.js</a>.</p>
<!-- /wp:paragraph -->
```

### Example 3 — Changelog page (default template)

```html
<!-- wp:paragraph -->
<p>Major releases and the bundles they shipped. For per-commit detail, see the <a href="https://github.com/brndle-themes/brndle/commits/main">commit log</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:brndle/timeline {
  "iconStyle": "numbered",
  "connector": "solid",
  "density": "comfortable",
  "items": [
    {
      "date": "May 2026",
      "title": "v2.1 editorial blocks",
      "description": "Code, pull quote, timeline, tabs/accordion. Library: 14 → 18 blocks. WAI-ARIA on every interactive surface. Lazy-loaded highlight.js."
    },
    {
      "date": "May 2026",
      "title": "v2.0 audit baseline",
      "description": "Brndle-styled comments template, 404/search polish, back-to-top button, print stylesheet, last-updated date pill. Yoast/Rank Math Person schema enrichment."
    },
    {
      "date": "May 2026",
      "title": "v1.9.x — Local avatars + mega menus",
      "description": "Self-hosted avatars, per-user social meta + role, fully-tabbed mega menu, sticky header modes, header search slot."
    },
    {
      "date": "May 2026",
      "title": "v1.5.x — Blog homepage sections",
      "description": "News-portal-style stacked category sections layout when blog is the front page. 7 visual styles."
    }
  ]
} /-->
```

---

## Authoring rules (don't violate these)

1. **Pick one register per content well.** Marketing blocks (hero, features, pricing) and editorial blocks (code, pull-quote, timeline, tabs-accordion) shouldn't sit in the same article column. Either you're building a landing page (marketing) or you're writing a long-form post (editorial).

2. **Use `brndle/faq` for SEO FAQ.** Use `brndle/tabs-accordion` (accordion mode) for non-SEO disclosure. Don't use `brndle/tabs-accordion` for true FAQ — you'll lose the `FAQPage` schema benefit.

3. **Always provide `quote` for pull-quote, `code` for code, `items` for timeline / tabs-accordion / faq.** Empty values cause the block to render nothing — silent failure.

4. **Pick the right page template.**
   - Landing pages → `template-landing.php`
   - Blog posts → default
   - Custom creative → `template-canvas.php`
   - Pages with dark hero → `template-transparent.php`

5. **Don't paste raw `<svg>` markup into block content.** Every Brndle icon goes through Lucide via `<x-icon>` (Blade frontend) or `@wordpress/icons` (editor JSX). Custom SVGs break the design language.

6. **Don't override block CSS by injecting `<style>` tags into post content.** The theme tokens are in `--color-*` variables; touch those at the theme level (admin → Brndle → Colors), not per-page.

7. **Editorial blocks default to the article column width (700px).** Use `align: "wide"` or `align: "full"` to break out — but only for the marketing register. Editorial blocks rarely need to.

---

## What NOT to build with these blocks

- **Multi-step forms** — `brndle/lead-form` is intentionally one-page. For multi-step, use a dedicated form plugin (Gravity Forms, Fluent Forms).
- **E-commerce product grids** — Brndle isn't WooCommerce-focused. Use WooCommerce blocks for product UI.
- **Image galleries with lightbox** — use `core/gallery` plus a lightbox plugin. Brndle doesn't ship a lightbox.
- **Booking widgets / calendars** — out of scope; integrate via a plugin.
- **Anything that needs JavaScript-driven server data fetching** — Brndle blocks render server-side. Don't try to make them dynamic with client-side fetch loops.

---

## Verification checklist before publishing

For any AI-generated page, sanity-check:

- [ ] All required attributes are filled (no empty `title`, `code`, `quote`, `items`).
- [ ] Block JSON parses cleanly. Run `wp post list --post_type=post --field=post_content` and pipe through any JSON validator on the comment-attribute substring if unsure.
- [ ] Page template is set correctly via `_wp_page_template` post meta.
- [ ] If using `brndle/code`, the `language` value is in the curated 18-language list.
- [ ] If using `brndle/timeline` with `iconStyle: "lucide"`, every `items[].icon` is a curated Lucide name.
- [ ] If using `brndle/pull-quote` with `variant: "outset"`, the page isn't on the narrow `sidebar` single-post layout (it'll auto-collapse to centered, but be aware).
- [ ] If embedding HTML in `tabs-accordion` content, only `<strong>`, `<em>`, `<a>`, `<br>`, `<code>` survive `wp_kses` server-side. Anything else gets stripped.
- [ ] After publish, view the URL on desktop AND mobile. Brndle is responsive, but content choices (very long titles, missing images) can break specific layouts.

---

## Where to go next

- **Building a new block** → `plans/2026-05-04-v2.1-editorial-blocks.md` shows the v2.1 pattern. Follow the same shape: `block.json` + `blocks/src/{name}.js` (editor) + `resources/views/blocks/{name}.blade.php` (Blade) + `resources/css/blocks/{name}.css` (scoped CSS) + optional `blocks/src/{name}-view.js` (frontend controller).
- **Changing how an existing block renders** → edit the Blade view in `resources/views/blocks/`. Clear the Acorn view cache: `rm -rf wp-content/cache/acorn/framework/views/*.php` (or just edit a file — cache invalidates by mtime).
- **Adding a new theme setting** → `app/Settings/Defaults.php` (add to `all()` + `schema()` + the appropriate type-keys array) → `admin/src/tabs/{Tab}.jsx` (add UI) → `app/View/Composers/Theme.php` (expose to Blade) → run `node bin/check-settings-consistency.mjs` to verify.
- **Releasing a new version** → bump `style.css` `Version:` + `readme.txt` `Stable tag:` + Changelog → `./bin/release.sh 2.x.y` → `gh release create v2.x.y`. The release script handles the build pipeline.
