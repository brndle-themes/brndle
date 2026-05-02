# Plan: Blog Homepage Sections (v1.5.0)

**Date:** 2026-05-02
**Author:** Claude Code (with Varun)
**Target release:** Brndle v1.5.0
**Effort:** ~2 focused days

## Why

When a Brndle site sets the blog (latest posts) as the homepage, today every post renders through the same archive layout (`grid|list|magazine|editorial|minimal`). That works for a niche blog. It does not work for a news-portal style site that wants the homepage to feel like a magazine front page: a featured section on top, then sections per primary category each with its own visual treatment. attowp.com is the anchor case.

This is also a long-term solution rather than a per-site theme override: any Brndle site can opt into the new layout from admin, configure their sections, and ship a magazine-style homepage without touching code.

## What we are NOT doing

- We are not replacing the existing 5 archive layouts. Sections is a sixth option, used only when the user picks it AND the blog is the homepage.
- We are not building a full block-based homepage editor. That is FSE territory and out of scope. This is a settings-driven layout for a specific use case.
- We are not adding per-post-type support yet. Top-level categories only. Custom taxonomies can come later.

## Architecture

### Setting shape

Add to `app/Settings/Defaults.php`:

```php
// New defaults
'homepage_sections' => [
    ['category_id' => 0, 'style' => 'featured-hero',  'count' => 3, 'show_title' => true,  'show_view_all' => true],
    ['category_id' => 0, 'style' => 'grid-3col',      'count' => 6, 'show_title' => true,  'show_view_all' => true],
    ['category_id' => 0, 'style' => 'magazine-strip', 'count' => 5, 'show_title' => true,  'show_view_all' => true],
    ['category_id' => 0, 'style' => 'list-with-thumb','count' => 4, 'show_title' => true,  'show_view_all' => true],
],
'homepage_sections_enabled' => false,  // explicit opt-in
```

Schema metadata:

```php
'homepage_sections_enabled' => [
    'section' => 'blog-homepage',
    'label'   => 'Use sections layout when blog is the homepage',
    'control' => 'toggle',
],
'homepage_sections' => [
    'section' => 'blog-homepage',
    'label'   => 'Homepage sections',
    'control' => 'sections-builder',  // new admin control type
],
```

`category_id = 0` means the section is unconfigured and will skip-render.

### Style components

Build seven Blade partials in `resources/views/partials/sections-styles/`. Each receives `$category`, `$posts`, `$config` (the row), and renders a self-contained `<section>`.

| Style key            | Layout                                          | Mobile behavior            |
|----------------------|-------------------------------------------------|----------------------------|
| `featured-hero`      | 1 large left + 2 stacked right (3 posts total)  | Stack vertically           |
| `grid-3col`          | Uniform 3-col grid                              | 1-col on mobile            |
| `magazine-strip`     | 1 left feature + 4 small stacked right          | All stacked                |
| `list-with-thumb`    | Full-width rows: thumb left, title + excerpt    | Thumb on top               |
| `mixed-2x2`          | 1 hero card + 3 small in 2x2 grid               | All stacked                |
| `ticker`             | Horizontal scroll of cards (snap)               | Same behavior, narrower    |
| `editorial-pair`     | 2 large cards side-by-side                      | Stack to 1-col             |

All styles use Tailwind utilities, follow the existing color tokens (`bg-surface-primary`, `text-text-primary`, etc), and respect dark mode via `dark:` variants.

### Routing

`resources/views/index.blade.php` change:

```blade
@php
  $isBlogHomepage = is_home() && (int) get_option('page_for_posts') === 0;
  $useSections = $isBlogHomepage
    && (bool) \Brndle\Settings\Settings::get('homepage_sections_enabled', false);
@endphp

@if ($useSections)
    @include('partials.archive.sections')
@else
    @include('partials.archive.' . $layout)
@endif
```

`resources/views/partials/archive/sections.blade.php`:

```blade
@php($sections = (array) \Brndle\Settings\Settings::get('homepage_sections', []))

@foreach ($sections as $config)
  @php
    $categoryId = (int) ($config['category_id'] ?? 0);
    if ($categoryId <= 0) continue;
    $category = get_category($categoryId);
    if (! $category) continue;
    $posts = get_posts([
      'category'       => $categoryId,
      'posts_per_page' => max(1, (int) ($config['count'] ?? 4)),
      'post_status'    => 'publish',
    ]);
    if (empty($posts)) continue;
    $style = in_array($config['style'] ?? '', $allowedStyles, true)
      ? $config['style']
      : 'grid-3col';
  @endphp
  @include('partials.sections-styles.' . $style, [
    'category' => $category,
    'posts'    => $posts,
    'config'   => $config,
  ])
@endforeach
```

### Admin UI

New admin tab: **Brndle → Blog Homepage**

Components:
- Toggle: "Use sections layout when blog is set as homepage"
- Section list (drag-drop reorder, persisted as ordered array):
  - Category picker (dropdown of top-level categories from REST `/wp/v2/categories?parent=0`)
  - Style dropdown with inline thumbnail
  - Post count slider (1-10)
  - "Show title" toggle
  - "Show view all link" toggle
  - Delete button
- "Add section" button (appends a new `featured-hero` row with category_id=0)
- Live preview iframe (optional, v1.6 — skip in 1.5)

Persistence via existing `brndle/v1/settings` REST endpoint. The sanitizer in `app/Settings/Sanitizer.php` validates the section array.

## File-by-file changes

### New files

- `resources/views/partials/archive/sections.blade.php` (router)
- `resources/views/partials/sections-styles/featured-hero.blade.php`
- `resources/views/partials/sections-styles/grid-3col.blade.php`
- `resources/views/partials/sections-styles/magazine-strip.blade.php`
- `resources/views/partials/sections-styles/list-with-thumb.blade.php`
- `resources/views/partials/sections-styles/mixed-2x2.blade.php`
- `resources/views/partials/sections-styles/ticker.blade.php`
- `resources/views/partials/sections-styles/editorial-pair.blade.php`
- `admin/src/tabs/BlogHomepage.jsx` (admin tab component)
- `admin/src/components/SectionRow.jsx`
- `plans/2026-05-02-blog-homepage-sections.md` (this file)

### Modified files

- `app/Settings/Defaults.php` — add `homepage_sections_enabled`, `homepage_sections` defaults + schema
- `app/Settings/Sanitizer.php` — sanitize section array, clamp count, validate style enum
- `resources/views/index.blade.php` — branch into sections when enabled + blog is homepage
- `admin/src/App.jsx` — register the new tab
- `style.css` — version bump 1.4.2 → 1.5.0
- `readme.txt` — changelog entry + Stable tag
- `CLAUDE.md` — add to "What's Available" + "Recent Changes"

## Verification

Per the project rule: every plan item that touches frontend/CSS/template gets a per-item browser check at 1280px AND 390px viewports.

Per-style check matrix:

| Style                | Desktop check | Mobile check |
|----------------------|---------------|--------------|
| `featured-hero`      | YES           | YES          |
| `grid-3col`          | YES           | YES          |
| `magazine-strip`     | YES           | YES          |
| `list-with-thumb`    | YES           | YES          |
| `mixed-2x2`          | YES           | YES          |
| `ticker`             | YES           | YES          |
| `editorial-pair`     | YES           | YES          |

End-to-end check on attowp staging with 4 sections configured (Trends & News, CMS Platforms, WordPress Development, Tutorials & Guides). Confirm:

- All sections render with correct posts
- Empty section (category with 0 posts) skips silently
- Reordering in admin UI persists and re-renders correctly
- Toggle off → falls back to existing magazine layout
- Toggle on, no sections configured → renders nothing, page does not break

## Defaults shipped

For fresh installs (no homepage_sections set), the settings tab shows an empty list with a "Use suggested defaults" button that pre-fills:
1. featured-hero on the most-populated top-level category
2. grid-3col on the second most-populated
3. magazine-strip on the third
4. list-with-thumb on the fourth

Determined at preview time by querying `wp_term_taxonomy` for the top 4 by `count`.

## Release path

1. Branch `feature/blog-homepage-sections` (already on main locally; squash-merge later)
2. Implement Phase 1 (settings)
3. Implement Phase 2 (style partials, one at a time, browser-verify each before next)
4. Implement Phase 3 (router + index.blade.php)
5. Implement Phase 4 (admin React)
6. Phase 5: full QA, blade compile dry-run, journey E2E
7. Bump to 1.5.0, write changelog, build zip, publish release

## Out of scope (future)

- Custom taxonomy support (tags, author, custom tax)
- Live preview iframe
- Per-section background color override
- Per-section CTA block
- Schedule a section to swap on a date

## Risk notes

- **Old sites with magazine layout active**: completely unaffected — `homepage_sections_enabled` defaults to false, so behavior is identical until the user opts in
- **Empty sections**: must skip-render so a misconfigured section does not produce dead whitespace
- **WP_Query in loop**: each section runs `get_posts()`, which is fine for 4-6 sections but should not be extended past ~10. If we ever need more, batch into a single query partitioned by category in PHP
- **Admin UI complexity**: drag-drop reorder is the only non-trivial interaction; use `@dnd-kit/core` (already a Brndle admin dep) for consistency with the existing settings UI

## Open decisions (to revisit during implementation)

1. Should the "featured-hero" always be the first section (pinned), or just the user's first row by convention? Default to convention; revisit if confusing.
2. Should the sections layout work on category archives too, or only on the blog homepage? Phase 1: blog homepage only. Phase 2: optional per-archive override.
3. Do we want an additional `style: 'mixed-author'` for sites that publish multi-author? Defer to a 1.6.x update if anyone asks.
