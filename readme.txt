=== Brndle ===
Contributors: brndlethemes
Tags: blog, custom-logo, custom-menu, featured-images, full-width-template, theme-options, translation-ready
Tested up to: 6.8
Stable tag: 1.9.0
Requires at least: 6.6
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enterprise WordPress theme with AI-powered landing pages, Blade templating, and Tailwind CSS. Zero page builders. Lighthouse 100.

== Description ==

Brndle is a free, open-source WordPress theme for agencies. One theme, unlimited client sites — built on Sage / Acorn (Laravel Blade + Tailwind CSS v4 + Vite 7) with 14 server-rendered Gutenberg blocks for landing pages.

* 12 color schemes
* 8 font pairs
* 8 header styles, 6 footer styles
* 5 archive layouts, 8 single post layouts
* 4 page templates (default, landing, canvas, transparent)
* Dark mode (toggle, system, or off)
* 14 custom landing-page blocks, all server-rendered
* AI landing-page generation via Claude Code
* Full-page Lighthouse 100 baseline
* WPML / Polylang / Yoast / RankMath / WooCommerce compatible

== Installation ==

1. Download the latest `brndle-x.x.x.zip` from the GitHub releases page.
2. In WP Admin, go to Appearance → Themes → Add New → Upload Theme.
3. Upload the zip and activate.
4. Configure under Brndle in the admin sidebar.

No build tools required for end users — the release zip ships compiled assets.

== Changelog ==

= 1.9.0 =
* **New: Sticky header modes (M4.A of mega-menu plan).** Setting `header_sticky_mode` adds 4 scroll behaviors that work uniformly across all 8 header styles: `static` (no sticky — explicit override even when a header style has its own sticky baked in), `sticky-fixed` (always visible, default), `sticky-fade` (subtle blur + shadow nudge appears once the user scrolls past 40px), and `sticky-hide-on-scroll` (hides on scroll-down, reveals on scroll-up — common product-site pattern). Mode is selected from the Header tab in admin → Behavior. Vanilla JS scroll watcher (~1KB) on a single requestAnimationFrame loop; passive listener; honors `prefers-reduced-motion` (kills the slide / fade transitions but keeps the sticky position). Reveals header on `:focus-within` so keyboard users navigating into the header don't lose track when it's hidden.
* **New: Header search slot (M4.B of mega-menu plan).** Setting `header_search_enabled` adds a fixed-position search-icon button that opens a popover with WordPress's standard `get_search_form()` (so any plugin-customized search — Yoast, RankMath, Algolia — picks up automatically). Popover is keyboard + click-outside dismissible (Esc closes + returns focus to the trigger; click-outside closes). Search input gets the same 3px accent focus ring + accent submit button as the rest of Brndle's design system. Trigger is rendered once at the layout level rather than inside each header style's markup so it works across all 8 styles without per-style edits.
* **Plan progress.** With v1.9.0 the four-milestone mega-menu plan is delivered: M1 (foundation, 1.6.0), M2 (mega + admin + featured + widget area + auto-posts, 1.7.0 + 1.7.1), M3 (tabbed mega + conditional visibility, 1.8.0), M4 (sticky modes + search, this release). Mini-cart and user avatar slots — originally planned for M4 — are deferred to a future v1.9.x as standalone integrations because each needs its own domain depth (WooCommerce cart fragments AJAX; auth-aware avatar dropdown). Brndle now ships a feature-complete mega menu system with three rendering modes (standard / flyout / mega), three content sources per mega panel (manual / widget area / auto-posts), tabbed mega, conditional visibility, and four sticky modes — comparable to the free tier of Max Mega Menu plus the visual depth of paid premium themes.

= 1.8.0 =
* **New: Tabbed mega menus (M3.A of mega-menu plan).** Per mega-enabled menu item: tick "Render as tabbed mega" and the children become a left-rail tab list, while their grandchildren become the right-panel content. Pattern matches UberMenu's tabbed mode + the Linear / GitHub product menu UX. Full WAI-ARIA tabs implementation: role=tablist with vertical orientation, role=tab buttons with aria-selected + aria-controls, role=tabpanel divs with aria-labelledby + hidden attribute. Click selects a tab; ↑/↓/←/→ arrow keys navigate between tabs; Home/End jump to first/last; Enter/Space activate; tabindex=-1 on inactive tabs so Tab cycles to the next focusable element rather than every tab.
* **New: Conditional visibility per item (M3.B of mega-menu plan).** Five new visibility controls per menu item under "Brndle: Item details": Visibility — auth state (Anyone / Only logged-in / Only logged-out) plus three viewport toggles (Hide on desktop / tablet / mobile). Auth state hides server-side — items meant for logged-in users are never rendered into the DOM for anonymous visitors (so the markup can't be sniffed). Viewport hiding is CSS-driven via a `data-brndle-hide-on` attribute the walker emits + `@media` rules at Tailwind's 768/1024 breakpoints. The CSS layer correctly handles hiding inside both the desktop dropdowns and tabbed mega panels (including descendant menu items reached via `renderMegaChild`).
* **Bumps to v1.8.0** rather than 1.7.2 because tabbed mega is a substantive new rendering path with its own walker branch + JS controller, not a polish patch.

= 1.7.1 =
* **New: alternate content sources for mega menus (M2.D + M2.E of mega-menu plan).** Each mega-enabled menu item now picks one of three sources: `manual` (this menu's children, current behavior), `widget-area` (drop any WP widget into the mega panel), or `auto-posts` (latest N posts from a chosen category as cards).
* **Widget-area source.** When selected, Brndle dynamically registers a sidebar named `brndle-mega-{menu_item_id}` so it appears in Appearance → Widgets and in the Customizer's widget panel. The walker calls `dynamic_sidebar()` for that ID inside the mega panel — drop in Search, Recent Posts, Custom HTML, Image, or any third-party widget. Per-item sidebars (rather than one shared) let different mega panels carry different widget palettes (Stripe-style "products" mega vs Linear-style "resources" mega) without conditional visibility hacks.
* **Auto-posts source.** Per mega-enabled item: pick a category + count (1-12). Walker queries `get_posts()` with `posts_per_page=N`, primes the thumbnail cache via `_prime_post_caches()` (so featured images don't N+1 on the attachments table), and renders the posts as cards (thumbnail + title + date) round-robin distributed across the content columns. Honors the `_brndle_mega_featured_image` slot — featured block still renders as the last column when set.
* **Admin UI.** Source dropdown added to the "Brndle: Mega menu" fieldset. Auto-posts category + count fields appear below it; they're inert when source is `manual` or `widget-area`. Source value is sanitized to the fixed enum (manual / widget-area / auto-posts), defaults to `manual`.
* **Verified.** Auto-posts: 4 cards in 2 content columns + featured = 3-col panel as configured. Widget-area: search + text widgets render with uppercase widget titles and the featured block on the side. Both modes work alongside the existing manual mode and the bottom CTA row.

= 1.7.0 =
* **New: Mega menu rendering + admin UI (M2 of mega-menu plan).** v1.6.0 shipped the walker scaffold and dropdown CSS; v1.7.0 makes mega menus actually configurable and usable from the WP menu editor. Tick "Display children as mega menu" on any top-level menu item, choose 2-4 columns, optionally add a featured image block + bottom CTA, and the children render in a viewport-centered mega panel with per-item icons (Lucide), descriptions, and badges (`NEW` / `BETA` / `PRO` / custom). Children can be assigned to specific columns via the `_brndle_column` meta or auto-distributed round-robin.
* **Walker.** `Brndle\Navigation\MegaMenuWalker` overrides `display_element()` for top-level items with `_brndle_mega_menu = 1`. Renders a `<div class="brndle-mega" data-cols="N">` containing a CSS Grid columns container + featured block (when set) + bottom CTA row. Children grouped by `_brndle_column` (1-N) or auto round-robin. Items with `_brndle_column_heading` render as small-caps `<h4>` section titles instead of links. Standard / flyout dropdowns continue to work for items without mega config — same walker handles all three modes.
* **Admin UI.** Two fieldsets render under each menu item in the menu editor (Appearance → Menus). "Brndle: Mega menu" — toggle, columns select, featured image (attachment ID), featured heading + description + URL, bottom CTA text + URL. "Brndle: Item details" — column assignment, column heading, Lucide icon picker (with `<datalist>` autocomplete from the installed icon set), 1-line description, badge.
* **Featured block + bottom CTA.** Featured block renders as the LAST grid cell so it occupies one column width naturally rather than full-panel-width — matches the Stripe / Shopify / Linear pattern. Image via `wp_get_attachment_image('medium')` with lazy loading. Bottom CTA row sits below the columns grid with a top border.
* **Positioning.** Mega panel uses `position: fixed` so it's viewport-centered (max-w-7xl) regardless of which top-level item triggered it. JS controller measures the trigger's bottom rect and sets `--brndle-mega-top` on every open so the panel tracks sticky / banner / glass header offsets and any future scroll-shrink behavior.
* **Lucide icon support.** Inline SVG read from `resources/icons/{name}.svg` (the existing Brndle Lucide set, ~50 icons). The admin icon picker offers an autocomplete `<datalist>` of all installed names. Empty / unknown icons render with no slot (no broken icon glyph).
* **Verified.** All 8 header styles × desktop (1280) / tablet (820) / mobile (390). Mega panel renders correctly through `glass` / `transparent` backdrop-blur (opaque own-bg). Mobile drawer keeps the disclosure-button accordion from M1; mobile mega is treated as a flat accordion (the panel layout is desktop-only).
* **Deferred to next.** M2.D widget-area mega slot (drop any WP widget inside a mega panel) and M2.E auto-populated post columns (latest N posts from a category as cards). Both queued for v1.7.1.

= 1.6.0 =
* **New: Multi-level menu support in header (M1 of mega-menu plan).** Until now, header navs that had nested menu items rendered the children inline as a raw `<ul>` because no CSS handled `.sub-menu`. v1.6.0 ships a custom `Brndle\Navigation\MegaMenuWalker` that produces production-grade dropdown markup, plus the styles + JS to make 2-level dropdowns and 3-level fly-outs work cleanly across all 8 header styles. Mobile drawers gain a disclosure-button accordion so parents with children can be expanded inline.
* **Walker.** Custom `Walker_Nav_Menu` subclass passed to all 15 `wp_nav_menu()` invocations in `resources/views/sections/header.blade.php`. Desktop context: emits additive `data-brndle-has-submenu`, `aria-haspopup`, `aria-expanded` on parents — markup is byte-identical to the default walker for items without children. Mobile context: appends a `<button data-brndle-disclosure aria-expanded="false">` next to each parent `<a>`, plus `[hidden]` on the nested `<ul.sub-menu>` so the disclosure JS can toggle a `max-height` accordion.
* **Per-menu-item meta scaffold (`_brndle_*` keys registered).** Foundation for upcoming mega-menu features. Keys registered: `_brndle_mega_menu`, `_brndle_mega_columns`, `_brndle_mega_featured_image`, `_brndle_mega_featured_heading`, `_brndle_mega_featured_description`, `_brndle_mega_featured_url`, `_brndle_mega_cta_text`, `_brndle_mega_cta_url`, `_brndle_column`, `_brndle_column_heading`, `_brndle_icon`, `_brndle_description`, `_brndle_badge`. Save handler in place; admin UI fields ship in M2.
* **CSS.** ~250 lines under `resources/css/app.css` adds: standard 2-level dropdown (position absolute, fade + slide reveal, accent-leaning chevron indicator that rotates open), 3-level fly-out right-of-parent, mega panel scaffolding (columns 2-4, featured block, bottom CTA, opaque bg through `glass`/`transparent` blur), z-index 55 between the header bar and any future modal. `(hover: none)` kills hover triggers on touch-primary devices; `prefers-reduced-motion` disables transitions. Breakpoint matches Brndle's existing `md:` (768px) so iPad portrait works correctly.
* **JS controller.** `resources/js/mega-menu.js` (3.13 KB) — vanilla, idempotent, lazily attached. Hover open with 100ms debounce + 200ms close grace, click parity for touch + keyboard, aria-expanded flip, click-outside + Esc closes, Tab + ↑/↓ arrow nav within open submenu. Handles disclosure buttons in mobile drawer — animates `max-height` from 0 to `scrollHeight` on open, reverses on close, restores `[hidden]` after the transition completes.
* **Audit + verification.** All 8 header styles (sticky, solid, centered, transparent, split, banner, glass, minimal) verified at desktop (1280px), tablet (820px iPad portrait), and mobile (390px iPhone 14 Pro) viewports. Six audit findings called out in `plans/2026-05-03-mega-menu.md` are addressed: drawer-vs-collapse mismatch, minimal style flat-list rendering, split-style two-row anchor positioning, glass/transparent backdrop-blur leak, mobile accordion as new behavior, inline-script coexistence with existing per-style mobile toggles.
* **Plan + milestones.** Full design doc at `plans/2026-05-03-mega-menu.md` covers M1 (this release) + M2 (widget areas + auto-populated post columns) + M3 (tabbed mega + conditional visibility) + M4 (header slot integrations). 31 capabilities mapped against Max Mega Menu, UberMenu, Pearl Mega Menu, Elementor Pro Mega for plugin parity.

= 1.5.8 =
* **Polish:** Brndle admin panel switched from a fixed pixel cap (was 960px → 1280px in earlier 1.5.x) to a fluid `max-width: 90%` with auto-centered margins. The panel now scales with the viewport — ~1568px wide on a 1920px screen, ~1152px on 1280px — with a 5% gutter on each side that prevents the card from running edge-to-edge while still using most of the available real estate. Resolves the "fixed-width feels cramped on wide displays" feedback after 1.5.7.

= 1.5.7 =
* **Bugfix:** "Posts to show" slider on Blog Homepage Sections was silently truncated by four of the seven style partials. Setting count=7 with magazine-strip rendered only 5 posts; featured-hero capped at 3; mixed-2x2 capped at 4; editorial-pair capped at 2. Each style had a hardcoded `array_slice(..., n, m)` from the original prototype that the count slider could not override. Removed the caps — every style now respects the count value end-to-end. Magazine-strip scales the right-column numbered list, featured-hero scales the sidebar stack, mixed-2x2 wraps additional cards into more grid rows, editorial-pair becomes a 2-col grid that stacks 3+ rows for higher counts. Sanitizer's 1-10 clamp on the slider value still applies as the upper bound.

= 1.5.6 =
* **Polish:** Ticker section no longer shows a native horizontal scrollbar at the bottom of the row. The bar read as visual noise on the magazine homepage and undermined the news-portal feel. Replaced with two overlay arrow buttons (prev / next, circular, accent on hover) that handle desktop scrolling. Touch + trackpad swipe still work as before. Buttons auto-hide when the row does not overflow, when scrolled to the start (prev), or when scrolled to the end (next). On touch-primary devices (`@media (hover: none)`) the buttons are hidden entirely so swipe is the only affordance. Vanilla JS controller (~1KB inline) with `prefers-reduced-motion` support.
* **Performance (large-site hardening):** Verified the Blog Homepage Sections layout against the cost profile of a 7k+ post site and made four structural fixes. (1) Auto-defaults `get_categories()` lookup is now transient-cached for 1 hour under `brndle_homepage_auto_cats` — the underlying query JOINs through `wp_term_relationships` and is the slow part on large blogs. Cache invalidates on category CRUD and on `brndle_settings` save; we deliberately do NOT invalidate on per-post save because the top-5 by count is stable and per-save invalidation would defeat the cache on busy editorial sites. (2) Each section primes the WordPress thumbnail cache via `_prime_post_caches()` before rendering, replacing the per-post N+1 query against `wp_posts` for featured images (saves ~30–80ms per section without object cache). (3) Sanitizer hard-caps `homepage_sections` at 12 entries and the render path enforces the same cap in case legacy data slips through — a 100-section misconfiguration would otherwise issue 100 SELECTs per pageview. (4) Admin REST fetch on the Blog Homepage tab uses `_fields=id,name,count,slug` and `hide_empty=true`, dropping the response from ~250KB to ~20KB on sites with 100+ categories.

= 1.5.5 =
* **Hotfix:** Brndle admin settings panel rendered an "Something went wrong loading the settings panel" error after upgrading to 1.5.4. Root cause: while restructuring the BlogHomepage tab in 1.5.3 the `const sections = ...` declaration was accidentally removed from the function body, leaving every reference to `sections.length`, `sections.map(...)`, etc. as a `ReferenceError` at runtime. webpack/wp-scripts compiled the bundle without complaining (no `no-undef` lint pass in the release flow), so the bug only surfaced when the React error boundary caught the throw on first render. Declaration is back; admin verified rendering with all 11 tabs on a single line + Blog Homepage tab loading + auto-fill triggering on toggle. Adding `npm run lint` to the release checklist as a structural fix to prevent this class of bug from shipping again.

= 1.5.4 =
* **Polish:** Dropped the alternating background tint on Blog Homepage Sections. On dark themes the 5% tinted slab read as a "card-within-card" boundary rather than a magazine flow break, and even on light themes it added visual weight without earning its keep. Sections now sit flush on the page background; differentiation comes from the layout choice of each section, the numbered kicker (01 / 02 / 03 …), and a hairline divider between blocks. The result reads as a magazine front page rather than stacked panels.

= 1.5.3 =
* **Improvement:** Blog Homepage admin tab now pre-populates the sections list with the magazine-flow defaults the moment you flip the toggle on. Previously the auto-defaults only fired on the frontend render path, so the admin showed an empty list with "No sections configured yet" even though the live homepage was rendering five sections. Now the admin and the frontend agree: toggling on materializes 5 editable rows (top 5 most-populated parent categories, mapped to featured-hero / grid-3col / magazine-strip / editorial-pair / ticker), and you can immediately reorder, swap, or remove them. Auto-fill triggers ONLY on the off→on transition; if you save an empty list the admin keeps respecting that.
* **Improvement:** "Apply suggested defaults" button now visible whenever the sections list is empty (after toggle is on). One click rebuilds the magazine-flow preset against your current top categories. Useful if you cleared sections to start fresh and want to re-fill.
* **Polish:** Tab strip in Brndle admin no longer wraps to 2 lines on standard 1440px screens. Added `!important` to the flex-wrap and white-space rules — `@wordpress/components` ships its own computed flex behavior that was winning over our overrides on certain WP versions. Now matches the 1.5.1 fix's intent reliably.

= 1.5.2 =
* **Improvement:** Blog Homepage Sections now "just work" the moment you flip the toggle on. Previously the layout required at least one section to be configured manually before it would activate; if the array was empty it silently fell back to the regular archive layout, leaving most sites stuck on the old uniform grid even after enabling the feature. Now when the toggle is on but no sections are configured, Brndle auto-picks the top five most-populated top-level (parent=0) categories and composes them as a real magazine front page: 01 lead story (featured-hero, 1 big + 2 stacked), 02 recent recap (grid-3col, 6 posts), 03 long read + numbered list (magazine-strip, 5 posts), 04 editorial spread (editorial-pair, 2 large), 05 trending strip (ticker, 8 posts). You can still configure manually for full control; auto-defaults only kick in when the section list is empty.
* **Improvement:** Each section is now wrapped in `.brndle-homepage-section` with magazine-style visual differentiation: alternating tonal backgrounds every other section (subtle, ~3% tint), section dividers between blocks, and a numbered kicker (01 / 02 / ...) inline with each category title. The goal is that scrolling the homepage reads as a layered front page rather than one long uniform grid. The kicker uses an accent-leaning color via `color-mix()` so it picks up whatever color scheme the site is on.
* **Improvement:** When sections layout is active, the redundant "Latest Posts" page header and the category filter pills are hidden. Each section already renders its own category heading + "view all" link, so a duplicate filter strip above them was visual noise. Pagination is also hidden in sections mode (sections show curated counts; per-category pagination lives on the category archive pages reached via "view all").

= 1.5.1 =
* **Polish:** Admin category labels in the new Blog Homepage tab were rendering REST-sourced names with HTML entities still encoded ("Security &amp; Performance" instead of "Security & Performance"). Now piped through `@wordpress/html-entities` `decodeEntities()` before display. The bug was admin-only — frontend Blade uses raw PHP `WP_Term->name` and was already correct.
* **Polish:** Brndle admin panel widened from 960px → 1280px max-width and tabs no longer wrap to a second line. With 11 tabs in the strip the old width forced "Site Identity", "Dark Mode", "Blog Archive", "Blog Homepage", and "Single Post" to break across lines on standard 1440px screens. Tab strip now uses `flex-wrap: nowrap` + `white-space: nowrap` + horizontal scroll fallback below 1024px viewport.

= 1.5.0 =
* **New: Blog Homepage Sections layout.** When the blog index is set as your front page, you can now opt into a news-portal-style homepage composed of stacked category sections instead of a single uniform archive layout. Each section renders posts from one top-level category in a chosen visual style. Seven styles ship: featured-hero (1 large + 2 stacked), grid-3col (uniform 3-column grid), magazine-strip (1 feature + 4 small with numbered list), list-with-thumb (full-width rows), mixed-2x2 (1 hero + 3 small), ticker (horizontal snap-scroll), and editorial-pair (2 large side-by-side). All seven styles are responsive (stack appropriately on mobile down to 390px), respect dark mode, and use the existing Brndle color tokens. Configure via admin Brndle → Blog Homepage: drag-order with up/down buttons, pick category + style + post count per row, toggle title and view-all link. Toggle off and the existing magazine archive layout takes over with no behavior change. The router falls back gracefully when a section's category has no published posts (the row skips silently rather than producing dead whitespace). See `plans/2026-05-02-blog-homepage-sections.md` for the full design and the file-by-file change list.
* **Internal:** New sanitizer for the homepage_sections array. Validates the style enum against `Defaults::homepageSectionStyles()`, clamps post count to 1-10, and casts category_id through `absint()`. Settings consistency CI updated to 47 keys (45 → 47).

= 1.4.2 =
* **Hotfix:** `resources/css/critical.css` is now wrapped in `@layer base { ... }` and `@layer utilities { ... }` cascade layers. Before this, the unlayered `img,picture,video,canvas,svg { display: block }` preflight rule sat outside any layer — and per CSS spec, unlayered styles always win over layered ones, regardless of specificity. That meant `.hidden { display: none }` (which Tailwind emits inside `@layer utilities`) was silently overridden, breaking every `dark:hidden` / `dark:block` toggle on the page. Most visible symptom on attowp.com: the header (and footer) rendered both the light and dark logos side-by-side because the `hidden` class on the inactive variant didn't take effect. Now that the critical preflight lives in `@layer base`, Tailwind's utilities (in `@layer utilities`) resolve correctly and dark-mode logo / image swaps work as designed. Pure CSS change — no settings migration, no template changes.

= 1.4.1 =
* **Hotfix:** the 1.4.0 release zip excluded `resources/css/critical.css` because `bin/release.sh` was stripping the entire `resources/css/` directory. With 1.4.0's `perf_critical_css` default flipped to true, fresh installs that triggered the inline-critical path then dropped `app.css` from `@vite` AND failed to read the missing critical.css — leaving pages with zero stylesheets (attowp.com surfaced this within minutes of the upgrade). Two-prong fix:
    1. `bin/release.sh` now keeps `resources/css/critical.css` in the zip; only the source `app.css` and `editor.css` files (which compile into `public/build/`) are excluded.
    2. Both layout templates (`app.blade.php`, `landing.blade.php`) now degrade gracefully — if `perf_critical_css` is on but `critical.css` can't be read, they fall back to render-blocking `app.css` instead of leaving the page CSS-less.
* If you're already on 1.4.0 and seeing unstyled pages, the immediate workaround is to toggle Critical CSS off from admin Brndle → Performance. Upgrading to 1.4.1 (or any later version) makes the toggle safe to leave on.

= 1.4.0 =
* Improvement: View Transitions and Critical CSS now default to ON for fresh installs and any site that hasn't yet opened the admin Performance tab. Brndle is primarily used for pages and blogs — soft-navigation between articles and faster first paint on landing pages are exactly the wins those sites need. Existing sites that already saved their settings keep their explicit values; the flip only affects new / never-touched installs. Both features remain admin-toggleable and respect `prefers-reduced-motion`.
* Improvement: Block attribute deprecation registry (`Brndle\Blocks\AttributeMigrations`) wired into every render callback. Sets the structural pattern needed to drop legacy attribute branches in Blade templates after future schema changes — same idempotent-function-per-version model as the settings migration registry from 1.3.3.
* Improvement: Settings schema metadata (`Defaults::schema()`) describes section / label / control / range for all 45 settings. Foundation for the future schema-driven admin UI; today the existing tabs ship unchanged. The settings consistency CI now also asserts every key has matching schema metadata.
* New: `bin/check-blade-compile.php` — pure-PHP dry-run that compiles every Blade template through Acorn's BladeCompiler and runs `token_get_all($source, TOKEN_PARSE)` on the output. Catches the comparison-table-bug class (compiler emits malformed PHP). Fixed six latent issues on first run: 5 unescaped CSS at-rules in inline `<style>` blocks (`@keyframes`, `@media` → `@@keyframes`, `@@media`) and the `<html @php(language_attributes())>` Blade-regex collision in 5 layouts (now raw `<?php language_attributes(); ?>`).
* New: `tests/e2e/journey.spec.js` — Playwright integration test (`npm run test:e2e`) covering frontend perf head tags, palette swap, LCP preload, admin settings, REST endpoint. Caught a real frontend regression on first run — `.brndle-section-dark <h1>` was rendering dark in light-mode sessions because Tailwind preflight broke inheritance. Fix shipped: explicit `color: inherit` rule.
* New: GitHub Actions workflow templates (`bin/github-actions/upstream-check.yml`, `release.yml`) ready for one-`cp` install — handles Mondays-09:00-UTC upstream drift tracking and manual workflow_dispatch version bumps.

= 1.3.3 =
* New: `<x-img>` Blade component with AVIF / WebP `<picture>` wrapper. Detects sibling `*.avif` / `*.webp` files next to local uploads and emits matching `<source>` elements; falls back to a plain `<img>` for external URLs or when no variant is on disk. Width / height / fetchpriority / loading flow through one `priority` prop. Adopted across hero, content-image-split, team, testimonials, and logos blocks — sites that already produce modern image variants serve them automatically.
* New: View Transitions API + soft-navigation controller (admin opt-in, default off). When enabled, emits `<meta name="view-transition" content="same-origin">` for native crossfade in Chrome / Edge 126+ and ships a 2.1 KiB vanilla-JS controller that intercepts same-origin clicks, fetches the destination, and swaps `<main>` + `<title>` + `<html lang>` inside `document.startViewTransition()`. Bails for downloads / external / cross-origin / reduced-motion / non-HTML / missing `<main>`. Dispatches `brndle:soft-nav` on document so block view-scripts can rebind.
* New: Critical CSS opt-in (admin Performance → Critical CSS, default off). Inlines a 3.2 KiB hand-curated stylesheet covering reset, body / html base, `.brndle-section-*` variants, hero layout primitives, and skip-link rule; defers the full `app.css` via `rel=preload` + `onload` swap so the first paint isn't render-blocked. `<noscript>` fallback restores blocking behaviour when JS is off.
* New: Lucide as the frontend SVG library (alongside `@wordpress/icons` for editor JSX). Curated `bin/copy-lucide-icons.mjs` writes Lucide SVGs into `resources/icons/` at build time; new `<x-icon name="kebab-case">` Blade component renders them inline with a single attribute. First adoption: FAQ summary plus icon. Convention recorded in CLAUDE.md — never use emoji as a UI affordance.
* Improvement: Settings migration registry — `Brndle\Settings\Migrations::all()` is now the single source of truth for schema upgrades. Each migration is a pure idempotent function applied in version order. Sets the structural pattern needed before any settings rename / restructure ships safely. `Settings::VERSION` stays at 1 today; the registry is ready for v2 the next time a key is restructured.
* Improvement: Settings consistency CI guardrail extended to 45 keys (was 43) — every PR fails fast if `Defaults.php` / admin tabs / code consumers drift apart.

= 1.3.2 =
* New: Speculation Rules — pages now emit a `<script type="speculationrules">` so Chrome and Edge prefetch same-origin links the user is likely to visit next, making navigation feel instant. Excludes `/wp-admin/*`, `/wp-login.php`, and any link with `rel="external"` or `data-no-prefetch`.
* New: LCP image preload — pages with a hero, content+image, or video-embed block emit `<link rel="preload" as="image" fetchpriority="high">` for the first such image, shaving 100–400 ms of LCP on hero-led layouts.
* New: Conditional integration preconnect — when a lead-form block is on the page and Mailchimp is configured, the page warms the DNS / TLS to the correct Mailchimp datacenter via `<link rel="preconnect">` + `dns-prefetch`. Cost is paid only on pages that actually use the integration.
* New: Filter hooks `brndle/perf/speculation_rules`, `brndle/perf/lcp_preload`, `brndle/perf/preconnects`, and `brndle/perf/preconnect_origins` for site-level opt-out / customisation.
* Improvement: Typography → Heading Scale Ratio admin slider now scales `<h1>`–`<h6>` inside `.prose` content (blog posts, page bodies). Block markup keeps its design-intent `text-[clamp(...)]` sizes.
* Improvement: Settings consistency CI guardrail (`bin/check-settings-consistency.mjs`) — every PR now fails fast if a key in `Defaults.php` lacks an admin tab field or a code consumer, or if an admin tab references a key that doesn't exist in defaults. Closes the kind of silent drift that produced the 1.3.0 dead settings.
* Chore: Drop the empty `resources/js/app.js` Vite shim. Phase-5 bundle hygiene from the perf roadmap — every page is now one fewer network request and the build manifest shrinks 1.36 → 0.82 KiB.

= 1.3.1 =
* Fix: Typography → Base Font Size and Heading Scale Ratio sliders now actually scale rendered output. Previously the admin saved + emitted CSS variables but no stylesheet read them. `html { font-size: var(--font-size-base) }` makes the rem cascade scale; `--text-h1` … `--text-h6` are now exposed as a calc() ramp from `--heading-scale` for opt-in heading sizing in custom CSS.
* Fix: Tailwind `dark:` modifier is now bound to brndle's `[data-theme="dark"]` toggle via a `@custom-variant`. Previously `dark:foo` only fired on `prefers-color-scheme: dark` — the toggle worked accidentally for the three `dark:` utilities currently used (`dark:hidden`, `dark:block`, `dark:prose-invert`) because each was manually re-implemented under `[data-theme="dark"]`. New utilities will now just work. The variant also covers `[data-theme="system"]` + OS-dark and the no-attribute fallback so child themes that drop the attribute keep OS-driven dark styling.
* Refactor: Block editor iframe styles now flow through Sage's idiomatic `Vite::asset('editor.css')` injection instead of a parallel enqueue path in BlockServiceProvider. `editor.css` `@import`s `app.css` so the iframe inherits the same Tailwind theme tokens, source globs, and `.brndle-section-*` rules as the frontend (-66 lines, single iframe-loading path).
* Chore: Adds `bin/check-upstream.sh` + a CLAUDE.md upstream-tracking section so framework drift from `roots/sage` is visible at a glance (Acorn 5→6, Vite 7→8, etc.).
* Chore: Adds `assets: ['resources/images/**', 'resources/fonts/**']` to the Vite Laravel-plugin input (Sage v11.2 pattern).
* Chore: Refresh `brndle.pot` to track the renumbered line references after the i18n + icon swap.

= 1.3.0 =
* New: Native media-library picker (`MediaUpload`) replaces raw URL inputs in hero, content-image-split, team, testimonials, and logos blocks. Includes alt-text input + URL fallback.
* New: Hero block exposes Dark, Light, and Gradient as separate inserter variations instead of a hidden select.
* New: FAQ block emits `FAQPage` JSON-LD schema for rich-result eligibility in search.
* New: Inserter previews (`example`) on all 14 blocks so the inserter shows representative content.
* New: 14 custom landing-page blocks documented (was 8): adds comparison-table, content-image-split, how-it-works, lead-form, team, video-embed.
* New: Hero `gradient` variant now renders (previously declared in `block.json` but unused).
* Improvement: Block editor canvas now loads the compiled `app.css` so Tailwind utilities + theme tokens resolve inside the iframe — fixes invisible dark-on-dark headlines and missing padding in the editor.
* Improvement: Lead form's submit script is now a proper registered viewScript instead of an inline `<script>` per block instance. Adds `aria-live="polite"` status region, focus management on success, safer entity decoding via `DOMParser`.
* Improvement: Editor strings across all 14 blocks are now wrapped in `__()` and a fresh POT contains 1110 msgid entries (was ~30) — translations register via `wp_set_script_translations()`.
* Improvement: Hover transitions, scroll reveals, and the eyebrow ping pulse now respect `prefers-reduced-motion`.
* Improvement: All 14 blocks register Gutenberg-native icons (cover, chartBar, gallery, media, listView, table, tag, quote, people, image, video, help, megaphone, envelope) from `@wordpress/icons` for consistent visual vocabulary.
* Improvement: `version` field added to every `block.json` for cache-busting.
* Improvement: Inline `style="color:#0a0a0a"` removed from hero CTA in favour of the new `.brndle-cta-inverse` class.
* Fix: Comparison-table block previously crashed on render — `@php($expr)` directive containing `===` produced malformed PHP. Now uses block-form `@php ... @endphp`.
* Chore: Bumps `@wordpress/scripts` to 32.x.

= 1.2.4 =
* Fix: Logo strip visibility, FAQ focus outline, and post nav entity encoding.
* Fix: Decode HTML entities in post titles and social share URLs.
* Refactor: Dark-mode toggle becomes a state machine; every surface uses theme tokens.

= 1.2.3 =
* Maintenance release.

= 1.2.2 =
* Maintenance release.

= 1.2.1 =
* Maintenance release.

= 1.2.0 =
* Initial public release.

== Upgrade Notice ==

= 1.4.1 =
**Critical hotfix for 1.4.0.** The 1.4.0 zip was missing `resources/css/critical.css` because the release script excluded the directory it lived in; with the new default `perf_critical_css = true`, fresh installs rendered with no stylesheets at all. 1.4.1 ships the file AND adds a layout safety net so any future asset-missing scenario falls back to rendering with `app.css` instead of breaking. Sites already on 1.4.0 with unstyled pages can either upgrade to 1.4.1, or temporarily toggle Brndle → Performance → Critical CSS off until they upgrade.

= 1.4.0 =
View Transitions and Critical CSS default to ON for fresh installs and never-saved settings — the flip is a no-op for existing sites that have already saved settings. To opt out on a fresh install, set `perf_view_transitions = false` and `perf_critical_css = false` from admin Brndle → Performance. Both features respect `prefers-reduced-motion` and fall back gracefully on unsupported browsers. Also bundles the structural plumbing shipped between 1.3.3 and 1.4.0 (block attribute migration registry, settings schema metadata, Blade compile dry-run script, Playwright E2E journey, workflow templates).

= 1.3.3 =
Adds AVIF / WebP `<picture>` wrapper across every block image, opt-in View Transitions for SPA-feel navigation, opt-in critical CSS to remove render-blocking on first paint, and Lucide icons for frontend Blade. Also lands the structural settings-migration registry needed for any future schema rename. All new features are off by default; turn them on from admin Performance.

= 1.3.2 =
Patch release — performance polish (speculation rules, LCP preload, conditional preconnect on lead-form pages) plus the Heading Scale slider now visibly affects blog content. New CI guardrail catches dead settings before merge. Backward compatible.

= 1.3.1 =
Patch release. Two dead admin settings (Base Font Size, Heading Scale) now actually affect rendered output. `dark:` Tailwind utilities are now bound to the brndle toggle so future dark: usage just works. Block editor iframe loading is simplified to align with upstream Sage. No breaking changes.

= 1.3.0 =
Major editor-experience improvements: native media picker, fixed dark-on-dark headlines in the canvas, FAQ JSON-LD schema, hero variations, and a comparison-table render bug fix. Backward compatible — existing posts keep rendering unchanged.
