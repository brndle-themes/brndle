# Plan: Theme audit roadmap (post-1.9.2)

**Date:** 2026-05-03
**Author:** Claude Code (with Varun)
**Effort:** ~13 hours across 3 release bundles

This plan captures gaps found during the post-1.9.2 audit of Brndle. Today the theme covers headers (8 styles + mega menu), footers (6 styles), archives (5 layouts), single posts (8 layouts), 14 blocks, settings + admin (47+ keys), local avatars, a complete mega menu system, blog homepage sections, performance modes, mobile drawers, color schemes, and i18n / RTL / WC compat. What's *missing* falls into three coherent bundles.

## Plugin compatibility constraints (applies to every bundle)

**Assumption: every Brndle site runs Yoast SEO or RankMath.** The theme MUST NOT compete with either plugin. Specifically:

- **Do NOT emit Article / BreadcrumbList / Person / Organization / WebSite / WebPage JSON-LD from the theme.** Both plugins emit complete schema graphs. Adding the theme's own schema produces duplicate `@type` entries that Google flags as "redundant" in Rich Results Test.
- **Do NOT emit Open Graph / Twitter Card / canonical / robots / hreflang meta tags.** Plugin territory.
- **Do NOT register a sitemap.** Plugin territory.
- **Breadcrumb UI** in the theme should *call* the plugin's breadcrumb function when present, not output its own structured data. Detection in `app/Compatibility/`:
  - Yoast: `function_exists('yoast_breadcrumb')` → render `yoast_breadcrumb('<nav>', '</nav>', false)`
  - RankMath: `function_exists('rank_math_the_breadcrumbs')` → call it
  - Fallback: visual-only breadcrumb (no `BreadcrumbList` JSON-LD).
- **Author social meta from `LocalAvatar`**: the v1.9.1 social URLs (`_brndle_twitter`, `_brndle_linkedin`, `_brndle_github`, `_brndle_website`) should *enrich* Yoast/RankMath Person schema via their filters, not bypass them. Hooks:
  - Yoast: `wpseo_schema_person` filter — append `sameAs` array.
  - RankMath: `rank_math/snippet/rich_snippet_person_entity` filter — append `sameAs` array.

This section is the rule. Anything in the bundles below that violates it must be removed before that bundle ships.

## Audit findings — current state

Inventoried 2026-05-03 against attowp.com (live deployment) + the local elementor.local install.

### What exists today

| Area | Coverage |
|---|---|
| Templates | 404 (12 lines, basic), search (24 lines, slim), single (8 layouts), index, page, 4 page templates |
| JSON-LD schema | Only FAQ block (theme-emitted, doesn't overlap plugins). Article / Breadcrumb / Person handled by Yoast or RankMath on every site. Theme should NOT emit competing schema — only enrich plugin schema with local-avatar social URLs. |
| Comments | No `comments.blade.php` partial. WP default styling. |
| Code blocks | None in the 14 blocks. Falls back to core Gutenberg `<pre>`. |
| Back-to-top | Not present. |
| Print stylesheet | No `@media print` rules anywhere. |
| Cookie consent | Relies on a plugin. |
| Site Health checks | Generic WP only. No Brndle-specific checks. |
| Last-updated date | Posts show publish date, never "Updated X". |
| Pattern showcase / style guide | None. Editors discover blocks by trial. |

### What works well

- 14 landing-page blocks (hero, features, pricing, etc.)
- Local avatars + per-user social meta + author bio (1.9.1 + 1.9.2)
- Mega menu with 3 modes + 3 content sources + tabbed + conditional visibility (1.6.0 → 1.9.0)
- Blog homepage sections with magazine-flow auto-defaults (1.5.0 → 1.5.8)
- Performance: critical CSS, view transitions, lazy loading, font preloading
- Sticky header modes + search slot
- 12 color schemes + 8 font pairs + dark mode

## Tier A — must-haves for v2.0 baseline

Eight gaps that every reader / search engine / print user notices today. Bundling as **v2.0** because schema markup + a comments template change visible behavior on every post.

| # | Gap | Why | Effort |
|---|---|---|---|
| A1 | Yoast / RankMath schema enrichment — append `sameAs` social URLs to Person schema; pass `articleSection` from primary category if missing | Local-avatar social meta (1.9.1) is invisible to Google today. Enrich the SEO plugin's Person entity rather than emitting our own. | ~1.5h |
| A2 | `comments.blade.php` partial | `comments_template()` falls back to WP defaults — visually 2010. | ~2h |
| A3 | 404 template polish — site search + popular posts + back-to-home CTA | Today 12 lines. Lost-visitor recovery. | ~1.5h |
| A4 | Search results page polish — empty state, result count, no-results treatment | Today 24 lines. No styling for the empty case. | ~1.5h |
| A5 | Code block w/ syntax highlighting + copy button | attowp.com is a technical blog. Every other post should have code samples. | ~2h |
| A6 | Back-to-top floating button | Universal expectation. ~30 lines vanilla JS + CSS. | ~30min |
| A7 | `@media print` stylesheet | Articles print messy today. | ~1h |
| A8 | Last-updated date display on posts | Visual UX signal. Yoast/RankMath both surface modified-date in their schema; this is just the on-page UI. | ~30min |

**Bundle 1 total: ~10.5h** for the comprehensive bundle, or split A1–A4 + A6–A8 (~7.5h) into **v2.0** and A5 code block into **v2.1** if shipping in two chunks reads cleaner.

## Tier B — substantial, not blocking

Editorial polish + privacy + admin UX. Group into **v2.1** or split if scope balloons.

| # | Gap | Why | Effort |
|---|---|---|---|
| B1 | Cookie consent (native, ~2KB, respects DNT) | GDPR. Today depends on plugin. | ~2h |
| B2 | Pattern showcase admin page | Style guide of all 14 blocks + 12 colors + 8 fonts. Helps editors discover what's available. | ~3h |
| B3 | Brndle-specific Site Health checks | Surface "critical CSS missing", "settings out of sync", "no posts in primary cat" via Site Health. | ~2h |
| B4 | Newsletter inline block | One-line subscribe between paragraphs. Lead-form exists but is full-form. | ~1h |
| B5 | Pull quote block | Standard editorial element; not in 14. | ~1h |
| B6 | Timeline / process steps block | Common landing-page element. | ~2h |
| B7 | Tabs / accordion block (separate from FAQ) | FAQ is opinionated + emits JSON-LD; need neutral version. | ~2h |
| B8 | Embed wrapper with consent gate | YouTube/Vimeo embeds load trackers on first paint. Click-to-load wrapper defers iframe. | ~1.5h |

**Bundle 2 total: ~14.5h** but realistic shipping path is to pick the 4 highest-leverage (B1, B2, B5, B7) for v2.1 (~8h) and defer the rest.

## Tier C — niche, ship if asked

| # | Gap | Effort |
|---|---|---|
| C1 | Highlight-and-share text selection | ~2h |
| C2 | Article series / "Part 2 of 5" navigation | ~2h |
| C3 | Bookmarks / favorites for logged-in users (BuddyPress integration) | ~3h |
| C4 | Print-optimized variant of single layouts | ~1h |
| C5 | Reading progress sticky bar (separate from existing scroll progress) | ~1h |
| C6 | Heading anchor links + auto-copy-on-hover | ~30min |
| C7 | "Sources / references" section at end of articles | ~1h |
| C8 | AI-content disclosure label (auto-detected via post meta) | ~1h |

**Bundle 3 total: ~12.5h** but pick on a per-customer basis.

## Recommended shipping order

| Release | Scope | Effort | Why this bundling |
|---|---|---|---|
| **v2.0** | A1 schema enrichment + A2 comments + A3 404 + A4 search + A6 back-to-top + A7 print + A8 updated-date | ~8.5h | Closes the "every post on every site" gaps. Major bump because comments template + reading-experience changes are visible everywhere; squat on the v2.0 number. |
| **v2.1** | A5 code block + B5 pull quote + B6 timeline + B7 tabs/accordion | ~7h | Editorial blocks. Fills the obvious gaps in the block library. Customer-facing — editors notice immediately. |
| **v2.2** | B1 cookie consent + B2 pattern showcase + B3 Site Health checks + B8 embed-with-consent | ~8.5h | Privacy + admin UX + editorial QoL. Less urgent for the reader but high-value for site owners. |
| **v2.3+** | Tier C pick-and-choose based on client requests | per item | Don't pre-build. |

## Phase-by-phase notes

### v2.0 — schema enrichment + base content templates

Schema enrichment strategy (NOT replacement — see "Plugin compatibility constraints" above):
- Extend the existing `app/Compatibility/Yoast.php` + add `app/Compatibility/RankMath.php`.
- Hook into `wpseo_schema_person` (Yoast) and `rank_math/snippet/rich_snippet_person_entity` (RankMath).
- For each Person entity, append `_brndle_twitter` / `_brndle_linkedin` / `_brndle_github` / `_brndle_website` to the `sameAs` array (de-duplicated).
- Optional: pass `_brndle_role` as `jobTitle` if the plugin's entity has no `jobTitle` set.
- Zero new schema graphs emitted. Pure enrichment.
- If neither Yoast nor RankMath is active, log an admin notice — do NOT fall back to emitting our own schema (out of scope; both plugins are assumed installed).

Comments template:
- `resources/views/partials/components/comments.blade.php` — wraps `<ol class="comment-list">` with Brndle-styled comment list, reply form, login wall.
- Custom `Walker_Comment` subclass for the per-comment markup so blockquotes / code / images inside comments inherit Brndle tokens.
- Include in single layouts after `comments_template()` call.

404 + search:
- Both consume the existing `index.blade.php` skeleton but render with empty-state UI.
- 404: heading + lost-visitor message + search form + 4-card recent-posts grid + back-to-home CTA.
- Search: heading "Search results for '{query}'" + result count + standard archive grid OR no-results state with related-search suggestions.

Last-updated:
- New setting `single_show_updated_date` (default on).
- In single layouts: if `get_the_modified_time('U') - get_the_time('U') > 86400`, show "Updated {modified}" inline with the publish date.

Back-to-top:
- New file `resources/js/back-to-top.js` (~25 lines).
- Renders a fixed bottom-right button via Blade partial included from layouts.
- Reveals after 400px scroll. Smooth-scroll on click. Respects prefers-reduced-motion.

Print stylesheet:
- `@media print { ... }` block in `app.css`. Hides header / footer / nav / share / comments / sidebar. Forces black-on-white text. Strips background images. Shows post title + author + publish date + content + URL footer.

### v2.1 — editorial blocks

Code block:
- New block `code` with `block.json`, `render.php`.
- Server-side: PHP-only Prism syntax highlighting (use `Highlight.php` library) OR client-side highlightjs lazy-loaded only when code block present on page.
- "Copy" button copies content via Clipboard API.
- Language picker in editor (PHP / JS / CSS / HTML / Bash / etc.).

Pull quote / Timeline / Tabs+Accordion:
- Standard block scaffolding via `@wordpress/create-block`.
- Pull quote: large indented text + optional cite + accent left-border.
- Timeline: vertical list of milestones (date + title + description), accent connecting line.
- Tabs/Accordion: two display modes — same data (label + content panels). Render-mode chosen in inspector.

### v2.2 — privacy + editorial QoL

Cookie consent:
- New file `app/Privacy/CookieConsent.php` + `resources/js/cookie-consent.js`.
- Banner CSS-only via `[data-brndle-consent]`. JS just toggles the data attr + writes a single localStorage flag.
- Respects `navigator.doNotTrack === '1'` (auto-decline).
- No third-party calls. No tracking. Just the consent record.
- Filter `brndle/consent_categories` for sites that want granular (Functional / Analytics / Marketing).

Pattern showcase admin page:
- New admin tab "Style guide" or new top-level "Brndle → Pattern showcase".
- Renders every block in default state + every color scheme + every font pair on one scrollable page.
- Editors / clients use it as reference; agencies use it for client demos.

Site Health checks:
- Hook `site_status_tests` filter. Add Brndle-specific tests: critical CSS file presence, settings consistency CI passes, primary nav menu set, expected image sizes registered.

Embed-with-consent:
- New Blade component `<x-embed>` that takes a YouTube / Vimeo URL.
- Renders a poster image + play button. iframe injected only on click.
- Reduces tracker load + LCP impact dramatically.

## Out of scope (do NOT build without specific request)

- Mini-cart / shop integrations — Brndle isn't WC-focused (existing memory rule).
- Avatar dropdown for logged-in users in header — was in mega-menu plan M4, dropped per scope.
- Live customizer preview — existing Customize panel covers most cases.
- Block editor full-site-editing variant — too much scope; stick with Sage-Blade for now.
- Custom analytics — server-side concern.

## Risk notes

- **Yoast / RankMath enrichment**: only the plugin's own filter API. Don't shadow / duplicate the entity. Test on three sites: Yoast active, RankMath active, neither active (graceful no-op + admin notice).
- **Cookie consent**: detect & defer to existing consent plugins (CookieYes, Cookie Notice, Complianz, GDPR Cookie Consent). When a known plugin is active, suppress the theme's banner + log an admin notice. Don't compete on a regulated surface.
- **Breadcrumb plugin detection**: same pattern — call Yoast / RankMath function when present, fall back to visual-only HTML. Never emit `BreadcrumbList` JSON-LD.
- **Walker_Comment custom class** can break with comment-meta plugins (subscribe-to-comments, etc.). Test against the most common ones. Keep markup additive — don't remove WP-default classes.
- **Code block syntax highlighting**: PHP-only Highlight.php adds ~150 KB to autoload. Compare with client-side highlightjs (~30 KB lazy-loaded). The client-side path is better for sites with one or two code samples per post; PHP-side wins on heavy technical blogs that want zero JS.

## Open decisions (resolve at start of each bundle)

**v2.0:**
1. Schema enrichment: should `_brndle_role` overwrite an existing `jobTitle` in Yoast/RankMath Person entity, or only fill when empty? → fill-when-empty default, filter to override.
2. Comments template should fall back to WP `comments_template()` defaults if `disable_comments_styling` is filtered on? → yes (escape hatch).
3. Print stylesheet should keep code blocks readable? → yes, force monospace + retain whitespace.
4. Last-updated threshold (24h) — too tight? → revisit if false-positive complaints. Default 24h.

**v2.1:**
1. Code block: PHP-side or client-side syntax highlighting? → client-side via highlightjs lazy-loaded; PHP-side is opt-in via filter.
2. Tabs/Accordion: combined block with display-mode toggle, or two separate blocks? → combined (less menu noise; one mental model).

**v2.2:**
1. Cookie consent: full vs minimal banner? → minimal default, full available via filter.
2. Pattern showcase: separate top-level menu or under Brndle settings? → under Brndle settings as a new tab.
