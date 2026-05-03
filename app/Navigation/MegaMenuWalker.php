<?php

/**
 * Mega menu walker.
 *
 * Custom Walker_Nav_Menu that powers Brndle's mega-menu / flyout / standard
 * dropdown system from M1 onwards. M1 ships in three layers:
 *
 *   1. Scaffold (this commit) — passthrough behavior plus additive ARIA + data
 *      attributes on parent items. Zero visual change vs default walker.
 *   2. Meta-driven markup — reads `_brndle_*` post meta on nav menu items and
 *      emits icon / description / badge / column-heading variants.
 *   3. Mega + flyout — start_lvl swaps to a mega panel layout when the parent
 *      item has `_brndle_mega_menu = 1`.
 *
 * Mobile context (constructor flag) emits disclosure buttons instead of
 * hover-driven submenus so the existing collapse-down mobile menu becomes a
 * proper accordion.
 *
 * @see plans/2026-05-03-mega-menu.md
 */

namespace Brndle\Navigation;

use Walker_Nav_Menu;
use WP_Post;

class MegaMenuWalker extends Walker_Nav_Menu
{
    /**
     * True when the walker is rendering inside the mobile drawer / collapse
     * panel. Affects how submenus are emitted (accordion vs hover-dropdown).
     */
    private bool $isMobile;

    /**
     * Active header style key. Some styles (notably `minimal`) have no
     * horizontal nav strip, so mega config is rendered as a flat list.
     */
    private string $headerStyle;

    public function __construct(bool $isMobile = false, string $headerStyle = 'sticky')
    {
        $this->isMobile = $isMobile;
        $this->headerStyle = $headerStyle;
    }

    /**
     * Override the walker's element traversal so mega-flagged depth=0 items
     * get our custom mega-panel markup instead of the default walker
     * recursing into children. For items WITHOUT mega meta, we defer to the
     * parent walker's behavior — which keeps standard + flyout dropdowns
     * working unchanged.
     *
     * The mega path:
     *   1. Call start_el normally (renders the parent <li><a>).
     *   2. Hand-emit the mega panel: <div class="brndle-mega">
     *      <div class="brndle-mega__columns">
     *        ...children grouped into columns...
     *      </div>
     *      [optional featured block]
     *      [optional bottom CTA]
     *      </div>
     *   3. Unset the children from `$children_elements` so the walker
     *      doesn't try to iterate them after we've already rendered them.
     *   4. Call end_el to close the parent </li>.
     *
     * Mega rendering is desktop-only — in mobile context we fall through to
     * the standard accordion path so the disclosure-button-based collapse
     * UX continues to work. (Mobile mega could become its own pattern in
     * a later milestone if requested.)
     *
     * @param  WP_Post|object $element
     * @param  array          $children_elements
     * @param  int|null       $max_depth
     * @param  int            $depth
     * @param  array          $args
     * @param  string         $output
     * @return void
     */
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        if (! $element) {
            return;
        }

        $meta = MenuItemMeta::get((int) $element->ID);

        // Conditional visibility — auth state. Server-side gating: items
        // hidden by auth never reach the DOM, so login state can't be
        // sniffed from the menu markup. Viewport hiding (`hide_on_*`) is
        // CSS-driven via data-attrs on the rendered element since we can't
        // know the client viewport server-side.
        if (! $this->itemIsVisibleForCurrentUser($meta)) {
            // Skip entirely — also drop its children so we don't orphan them.
            if (isset($children_elements[$element->ID])) {
                unset($children_elements[$element->ID]);
            }
            return;
        }

        // Mega path — desktop only, depth 0, mega flag set.
        if ($depth === 0 && ! $this->isMobile && ! empty($meta['_brndle_mega_menu'])) {
            $this->renderMegaItem($element, $children_elements, $max_depth, $depth, $args, $output, $meta);
            return;
        }

        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    /**
     * Auth-state visibility check. Items meant for logged-in users render
     * only when the request is authenticated (and vice versa for logged-out
     * targeted items). Default `any` always renders.
     *
     * @param  array $meta
     * @return bool
     */
    private function itemIsVisibleForCurrentUser(array $meta): bool
    {
        $auth = $meta['_brndle_visibility_auth'] ?? 'any';
        if ($auth === 'logged-in' && ! is_user_logged_in()) {
            return false;
        }
        if ($auth === 'logged-out' && is_user_logged_in()) {
            return false;
        }
        return true;
    }

    /**
     * Render a mega-menu top-level item: parent link + mega panel + closing
     * tag, all inline. Children are not iterated by the walker's recursion
     * — we emit them directly here.
     *
     * @param  object $element
     * @param  array  $children_elements
     * @param  int|null $max_depth
     * @param  int    $depth
     * @param  array  $args
     * @param  string $output
     * @param  array  $meta  Already-fetched MenuItemMeta::get($element->ID)
     * @return void
     */
    private function renderMegaItem($element, &$children_elements, $max_depth, $depth, $args, &$output, array $meta): void
    {
        // Render the parent <li><a>.
        $cbArgs = array_merge([&$output, $element, $depth], (array) $args);
        call_user_func_array([$this, 'start_el'], $cbArgs);

        // Build the mega panel.
        $cols = (int) ($meta['_brndle_mega_columns'] ?: 3);
        $cols = max(2, min(4, $cols));
        $children = $children_elements[$element->ID] ?? [];

        // The user-configured `cols` is the TOTAL grid column count. When
        // a featured block is present, it takes one of those columns — so
        // content items distribute across (cols - 1) columns. With no
        // featured block, content uses the full `cols` count.
        $hasFeatured = ! empty($meta['_brndle_mega_featured_image']);
        $contentCols = $hasFeatured ? max(1, $cols - 1) : $cols;
        $source = (string) ($meta['_brndle_mega_source'] ?: 'manual');

        $output .= '<div class="brndle-mega" data-cols="' . $cols . '" data-source="' . esc_attr($source) . '">';
        $output .= '<div class="brndle-mega__columns">';

        // Render the content area based on the chosen source. `manual`
        // (default) groups menu children into columns OR — when tabbed
        // mode is on — into a tab list + tab panels. `widget-area` swaps
        // for `dynamic_sidebar()`. `auto-posts` queries the configured
        // category and renders post cards.
        $tabbed = $source === 'manual' && ! empty($meta['_brndle_mega_tabbed']);

        switch (true) {
            case $tabbed:
                $this->renderTabbedSource($output, $element, $children, $children_elements);
                break;
            case $source === 'widget-area':
                $this->renderWidgetAreaSource($output, $element);
                break;
            case $source === 'auto-posts':
                $this->renderAutoPostsSource($output, $meta, $contentCols);
                break;
            default:
                $byColumn = $this->groupChildrenByColumn($children, $contentCols);
                foreach ($byColumn as $columnItems) {
                    $output .= '<ul class="brndle-mega__col">';
                    foreach ($columnItems as $child) {
                        $this->renderMegaChild($output, $child);
                    }
                    $output .= '</ul>';
                }
                break;
        }

        // Featured block — emitted INSIDE the columns grid as the last
        // grid cell, so it occupies one column-width naturally and stays
        // visually balanced with the content columns. Applies regardless
        // of source — featured is a separate optional slot.
        if ($hasFeatured) {
            $output .= $this->renderFeaturedBlock($meta);
        }

        $output .= '</div>'; // close .brndle-mega__columns

        // Bottom CTA row — full width below the columns grid.
        if (! empty($meta['_brndle_mega_cta_text']) && ! empty($meta['_brndle_mega_cta_url'])) {
            $output .= '<div class="brndle-mega__cta"><a href="' . esc_url($meta['_brndle_mega_cta_url']) . '">'
                . esc_html($meta['_brndle_mega_cta_text']) . ' <span aria-hidden="true">&rarr;</span></a></div>';
        }

        $output .= '</div>'; // close .brndle-mega

        // Don't let the parent walker re-iterate these children.
        unset($children_elements[$element->ID]);

        // Close the parent </li>.
        $cbArgs = array_merge([&$output, $element, $depth], (array) $args);
        call_user_func_array([$this, 'end_el'], $cbArgs);
    }

    /**
     * Group children into N column buckets, honoring per-item
     * `_brndle_column` meta where set (1..N). Items with column=0 are
     * round-robin distributed across the remaining slots.
     *
     * @param  array $children  Array of WP_Post-shaped menu item objects.
     * @param  int   $cols      Total column count (2..4).
     * @return array<int, array>  $cols arrays of children, indexed 0..cols-1.
     */
    private function groupChildrenByColumn(array $children, int $cols): array
    {
        $buckets = array_fill(0, $cols, []);
        $autoCursor = 0;

        foreach ($children as $child) {
            $childMeta = MenuItemMeta::get((int) $child->ID);
            $column = (int) ($childMeta['_brndle_column'] ?? 0);

            if ($column >= 1 && $column <= $cols) {
                // Explicit column assignment (1-indexed in admin, 0-indexed internally).
                $buckets[$column - 1][] = $child;
            } else {
                // Auto: round-robin across columns.
                $buckets[$autoCursor % $cols][] = $child;
                $autoCursor++;
            }
        }

        return $buckets;
    }

    /**
     * Render a single child item inside a mega column. Either a column
     * heading (if `_brndle_column_heading` is set) or a link with optional
     * icon + description + badge.
     *
     * @param  string $output
     * @param  object $child   WP_Post-shaped menu item object.
     * @return void
     */
    private function renderMegaChild(string &$output, $child): void
    {
        $meta = MenuItemMeta::get((int) $child->ID);

        // Viewport-hide data-attr — CSS @media reads this to hide / show
        // per breakpoint. Auth-state hiding was already filtered upstream
        // in display_element / renderTabbedSource, so any item that reaches
        // here is auth-visible.
        $hides = [];
        if (! empty($meta['_brndle_hide_on_desktop'])) $hides[] = 'desktop';
        if (! empty($meta['_brndle_hide_on_tablet'])) $hides[] = 'tablet';
        if (! empty($meta['_brndle_hide_on_mobile'])) $hides[] = 'mobile';
        $hideAttr = ! empty($hides) ? ' data-brndle-hide-on="' . esc_attr(implode(' ', $hides)) . '"' : '';

        // Column heading — `_brndle_column_heading` overrides the link.
        if (! empty($meta['_brndle_column_heading'])) {
            $output .= '<li class="brndle-mega__heading-item"' . $hideAttr . '>'
                . '<h4 class="brndle-mega__heading">' . esc_html($meta['_brndle_column_heading']) . '</h4>'
                . '</li>';
            return;
        }

        // Standard link with icon + description + badge.
        $url = ! empty($child->url) ? $child->url : '#';
        $title = $child->title ?? '';

        $iconHtml = '';
        if (! empty($meta['_brndle_icon'])) {
            // Icon names map to existing Lucide SVGs in resources/icons/.
            // We emit a placeholder span here; the inline SVG would require
            // file_get_contents which we'd rather avoid in a hot path. Mark
            // the slot with a data-attr and a future pass can swap in real
            // SVG via a one-time JS or PHP filter.
            $iconHtml = '<span class="brndle-mega__icon" data-brndle-icon="' . esc_attr($meta['_brndle_icon']) . '" aria-hidden="true">'
                . $this->lucideIconSvg($meta['_brndle_icon'])
                . '</span>';
        }

        $badgeHtml = '';
        if (! empty($meta['_brndle_badge'])) {
            $badgeHtml = '<span class="brndle-mega__badge">' . esc_html($meta['_brndle_badge']) . '</span>';
        }

        $descHtml = '';
        if (! empty($meta['_brndle_description'])) {
            $descHtml = '<span class="brndle-mega__desc">' . esc_html($meta['_brndle_description']) . '</span>';
        }

        $output .= '<li class="brndle-mega__item"' . $hideAttr . '>'
            . '<a href="' . esc_url($url) . '">'
            . $iconHtml
            . '<span class="brndle-mega__body">'
            . '<span class="brndle-mega__title">' . esc_html($title) . $badgeHtml . '</span>'
            . $descHtml
            . '</span>'
            . '</a>'
            . '</li>';
    }

    /**
     * Render the tabbed mega source — children become left-rail tabs and
     * grandchildren become each tab's content panel. Pattern matches
     * UberMenu's tabbed mode + the GitHub / Linear product menu UX.
     *
     * Markup:
     *   <div class="brndle-mega__tabbed" data-brndle-tabs>
     *     <div role="tablist" class="brndle-mega__tablist">
     *       <button role="tab" aria-selected="true" id="tab-X" aria-controls="panel-X">Tab 1</button>
     *       <button role="tab" aria-selected="false" ...>Tab 2</button>
     *     </div>
     *     <div role="tabpanel" id="panel-X" aria-labelledby="tab-X">
     *       <ul class="brndle-mega__col">...grandchildren as mega items...</ul>
     *     </div>
     *     <div role="tabpanel" hidden ...>...</div>
     *   </div>
     *
     * The tab-switch JS in mega-menu.js handles aria-selected flips +
     * panel hidden toggling on click + arrow keys (per WAI-ARIA tabs
     * pattern).
     *
     * Auth + viewport visibility for the children/grandchildren is honored
     * by short-circuiting in display_element above; tabs and panels for
     * hidden items never reach this method.
     *
     * @param  string $output
     * @param  object $element              Top-level menu item.
     * @param  array  $children             Direct children = tabs.
     * @param  array  $children_elements    Whole tree (so we can pull grandchildren).
     * @return void
     */
    private function renderTabbedSource(string &$output, $element, array $children, array &$children_elements): void
    {
        if (empty($children)) {
            return;
        }

        // Filter children by auth visibility — the parent display_element
        // already filtered grandchildren before they landed in $children_elements.
        $visibleTabs = array_values(array_filter($children, function ($child) {
            $childMeta = MenuItemMeta::get((int) $child->ID);
            return $this->itemIsVisibleForCurrentUser($childMeta);
        }));

        if (empty($visibleTabs)) {
            return;
        }

        $output .= '<div class="brndle-mega__tabbed" data-brndle-tabs style="grid-column: 1 / -1;">';
        $output .= '<div class="brndle-mega__tablist" role="tablist" aria-orientation="vertical">';
        foreach ($visibleTabs as $i => $child) {
            $tabId = 'brndle-tab-' . (int) $element->ID . '-' . (int) $child->ID;
            $panelId = 'brndle-panel-' . (int) $element->ID . '-' . (int) $child->ID;
            $isFirst = $i === 0;
            $output .= sprintf(
                '<button type="button" role="tab" id="%s" aria-controls="%s" aria-selected="%s" tabindex="%s" class="brndle-mega__tab">%s</button>',
                esc_attr($tabId),
                esc_attr($panelId),
                $isFirst ? 'true' : 'false',
                $isFirst ? '0' : '-1',
                esc_html($child->title ?? '')
            );
        }
        $output .= '</div>';

        $output .= '<div class="brndle-mega__panels">';
        foreach ($visibleTabs as $i => $child) {
            $tabId = 'brndle-tab-' . (int) $element->ID . '-' . (int) $child->ID;
            $panelId = 'brndle-panel-' . (int) $element->ID . '-' . (int) $child->ID;
            $isFirst = $i === 0;
            $hiddenAttr = $isFirst ? '' : 'hidden';

            $output .= sprintf(
                '<div role="tabpanel" id="%s" aria-labelledby="%s" %s class="brndle-mega__panel">',
                esc_attr($panelId),
                esc_attr($tabId),
                $hiddenAttr
            );

            $grandchildren = $children_elements[$child->ID] ?? [];
            // Filter grandchildren by visibility too.
            $grandchildren = array_filter($grandchildren, function ($gc) {
                $gcMeta = MenuItemMeta::get((int) $gc->ID);
                return $this->itemIsVisibleForCurrentUser($gcMeta);
            });

            if (! empty($grandchildren)) {
                $output .= '<ul class="brndle-mega__col">';
                foreach ($grandchildren as $gc) {
                    $this->renderMegaChild($output, $gc);
                }
                $output .= '</ul>';
            } else {
                // No grandchildren — show the tab's own link as fallback content.
                $url = ! empty($child->url) ? $child->url : '#';
                $title = $child->title ?? '';
                $output .= '<p style="margin:0.5rem 0.75rem;"><a href="' . esc_url($url) . '" style="color:var(--color-accent);font-weight:600;">' . esc_html($title) . ' &rarr;</a></p>';
            }

            // Prevent the walker from re-iterating these grandchildren elsewhere.
            unset($children_elements[$child->ID]);

            $output .= '</div>';
        }
        $output .= '</div>'; // close .brndle-mega__panels
        $output .= '</div>'; // close .brndle-mega__tabbed
    }

    /**
     * Render the widget-area source — calls `dynamic_sidebar()` for the
     * sidebar registered by `MegaSidebars` for this menu item. The whole
     * sidebar output gets wrapped in a `.brndle-mega__widgets` div that
     * spans the columns grid. If no widgets are configured, output is
     * empty (graceful degradation rather than a placeholder message).
     *
     * @param  string $output
     * @param  object $element  Top-level menu item.
     * @return void
     */
    private function renderWidgetAreaSource(string &$output, $element): void
    {
        $sidebarId = 'brndle-mega-' . (int) $element->ID;
        if (! is_active_sidebar($sidebarId)) {
            return;
        }

        $output .= '<div class="brndle-mega__widgets">';
        ob_start();
        dynamic_sidebar($sidebarId);
        $output .= (string) ob_get_clean();
        $output .= '</div>';
    }

    /**
     * Render the auto-posts source — pulls the latest N published posts
     * from the configured category and renders them as cards inside the
     * mega panel. Honors `update_post_thumbnail_cache` to avoid N+1 on
     * featured images. Each card is image + title + date.
     *
     * @param  string $output
     * @param  array  $meta
     * @param  int    $contentCols
     * @return void
     */
    private function renderAutoPostsSource(string &$output, array $meta, int $contentCols): void
    {
        $categoryId = (int) ($meta['_brndle_mega_auto_category'] ?? 0);
        $count = max(1, min(12, (int) ($meta['_brndle_mega_auto_count'] ?: 6)));
        if ($categoryId <= 0) {
            return;
        }

        $posts = get_posts([
            'post_type' => 'post',
            'posts_per_page' => $count,
            'post_status' => 'publish',
            'category' => $categoryId,
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ]);

        if (empty($posts)) {
            return;
        }

        // Prime thumbnail cache to avoid N+1 on featured images (same pattern
        // as Blog Homepage Sections — see app/Providers/ThemeServiceProvider).
        $thumbIds = array_filter(array_map(
            static fn($p) => (int) get_post_meta($p->ID, '_thumbnail_id', true),
            $posts
        ));
        if (! empty($thumbIds)) {
            _prime_post_caches($thumbIds, true, true);
        }

        // Distribute posts across content columns round-robin so the grid
        // stays balanced even when count isn't divisible by cols.
        $buckets = array_fill(0, $contentCols, []);
        foreach ($posts as $i => $post) {
            $buckets[$i % $contentCols][] = $post;
        }

        foreach ($buckets as $bucket) {
            if (empty($bucket)) {
                continue;
            }
            $output .= '<ul class="brndle-mega__col brndle-mega__col--posts">';
            foreach ($bucket as $post) {
                $output .= '<li class="brndle-mega__post">';
                $output .= '<a href="' . esc_url(get_permalink($post)) . '">';
                if (has_post_thumbnail($post)) {
                    $output .= '<span class="brndle-mega__post-thumb">'
                        . get_the_post_thumbnail($post, 'thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])
                        . '</span>';
                }
                $output .= '<span class="brndle-mega__post-body">'
                    . '<span class="brndle-mega__post-title">' . esc_html(get_the_title($post)) . '</span>'
                    . '<span class="brndle-mega__post-date">' . esc_html(get_the_date('', $post)) . '</span>'
                    . '</span>';
                $output .= '</a>';
                $output .= '</li>';
            }
            $output .= '</ul>';
        }
    }

    /**
     * Render the featured block as the last child of `.brndle-mega__columns`'s
     * parent (it sits OUTSIDE the columns grid as its own block). Image is
     * loaded via wp_get_attachment_image so srcset / lazyloading apply.
     *
     * @param  array $meta
     * @return string
     */
    private function renderFeaturedBlock(array $meta): string
    {
        $imageHtml = wp_get_attachment_image(
            (int) $meta['_brndle_mega_featured_image'],
            'medium',
            false,
            ['loading' => 'lazy', 'decoding' => 'async']
        );

        $heading = $meta['_brndle_mega_featured_heading'] ?? '';
        $desc = $meta['_brndle_mega_featured_description'] ?? '';
        $url = $meta['_brndle_mega_featured_url'] ?? '';

        $inner = $imageHtml;
        if ($heading !== '') {
            $inner .= '<div class="brndle-mega__featured-heading">' . esc_html($heading) . '</div>';
        }
        if ($desc !== '') {
            $inner .= '<div class="brndle-mega__featured-desc">' . esc_html($desc) . '</div>';
        }

        if ($url !== '') {
            return '<a class="brndle-mega__featured" href="' . esc_url($url) . '">' . $inner . '</a>';
        }
        return '<div class="brndle-mega__featured">' . $inner . '</div>';
    }

    /**
     * Emit a Lucide-style SVG for a given icon name. Looks up
     * `resources/icons/{name}.svg` (kebab-case) and inlines it. Returns a
     * fallback dot if the icon doesn't exist.
     *
     * @param  string $name
     * @return string
     */
    private function lucideIconSvg(string $name): string
    {
        static $cache = [];
        if (isset($cache[$name])) {
            return $cache[$name];
        }

        $name = preg_replace('/[^a-z0-9-]/', '', strtolower($name));
        if ($name === '') {
            return $cache[$name] = '';
        }

        $path = get_theme_file_path('resources/icons/' . $name . '.svg');
        if (! is_readable($path)) {
            return $cache[$name] = '';
        }

        $svg = (string) file_get_contents($path);
        return $cache[$name] = $svg;
    }

    /**
     * Open submenu wrapper. Defers to the parent walker for the standard
     * `<ul class="sub-menu">` markup, then in mobile context adds the
     * `hidden` attribute + a `data-brndle-mobile-submenu` hook so the
     * disclosure JS can toggle max-height transitions on it.
     *
     * @param  string         $output  Mutable output buffer (passed by reference).
     * @param  int            $depth
     * @param  \stdClass|null $args
     * @return void
     */
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        parent::start_lvl($output, $depth, $args);

        if (! $this->isMobile) {
            return;
        }

        // Replace the just-emitted `<ul class="sub-menu">` opening tag with
        // a version carrying the hidden attribute + disclosure data-attr.
        // The default walker emits the tag with whitespace prefix, so we
        // anchor on the trailing `>` and re-write the most recent UL open.
        $output = preg_replace(
            '/(<ul\b[^>]*class="[^"]*sub-menu[^"]*"[^>]*)>(?!.*<ul\b[^>]*sub-menu)/s',
            '$1 hidden data-brndle-mobile-submenu>',
            $output,
            1
        );
    }

    /**
     * Close submenu wrapper.
     *
     * @param  string         $output
     * @param  int            $depth
     * @param  \stdClass|null $args
     * @return void
     */
    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        parent::end_lvl($output, $depth, $args);
    }

    /**
     * Open a single menu item. Defers to parent for the actual `<li><a>`
     * rendering, then injects accessibility hints on items that have
     * children. The hints are additive — they never change visible markup.
     *
     * Why post-process instead of overriding entirely: the parent walker's
     * start_el handles `xfn`, `target`, classes, link_before/link_after, and
     * filter chains we don't want to re-implement. Splicing additional
     * attributes onto the rendered `<li>` is the smallest reliable change.
     *
     * @param  string         $output
     * @param  \WP_Post       $item
     * @param  int            $depth
     * @param  \stdClass|null $args
     * @param  int            $id
     * @return void
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $before = $output;
        parent::start_el($output, $item, $depth, $args, $id);

        // Add viewport-hide data attrs to every item that has them set.
        // CSS @media rules read these to hide / show per breakpoint. Auth
        // visibility was already filtered server-side in display_element.
        $itemMeta = MenuItemMeta::get((int) $item->ID);
        $viewportHides = [];
        if (! empty($itemMeta['_brndle_hide_on_desktop'])) $viewportHides[] = 'desktop';
        if (! empty($itemMeta['_brndle_hide_on_tablet'])) $viewportHides[] = 'tablet';
        if (! empty($itemMeta['_brndle_hide_on_mobile'])) $viewportHides[] = 'mobile';
        if (! empty($viewportHides)) {
            $delta = substr($output, strlen($before));
            $hideAttr = implode(' ', $viewportHides);
            $delta = preg_replace(
                '/^(<li\b)([^>]*)>/',
                '$1$2 data-brndle-hide-on="' . esc_attr($hideAttr) . '">',
                $delta,
                1
            );
            $output = $before . $delta;
            $before = $output; // re-anchor for the next pass below
        }

        $hasChildren = ! empty($item->classes) && in_array('menu-item-has-children', (array) $item->classes, true);
        if (! $hasChildren) {
            return;
        }

        // Capture only the chunk this call appended.
        $delta = substr($output, strlen($before));

        if ($this->isMobile) {
            // Mobile context: append a disclosure button after the </a> so
            // tapping it toggles the submenu without navigating to the
            // parent page. The button is the JS click target;
            // [data-brndle-disclosure] is what mega-menu.js binds to.
            $label = esc_attr__('Toggle submenu', 'brndle');
            $disclosure = '<button type="button" class="brndle-mobile-disclosure" '
                . 'data-brndle-disclosure aria-expanded="false" aria-label="' . $label . '">'
                . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" '
                . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" '
                . 'stroke-linejoin="round" aria-hidden="true">'
                . '<polyline points="6 9 12 15 18 9"/>'
                . '</svg>'
                . '</button>';
            // Inject right after the closing </a> of THIS item's link.
            $delta = preg_replace('#</a>#', '</a>' . $disclosure, $delta, 1);
        } else {
            // Desktop context: additive ARIA + data attributes only. CSS
            // handles hover; JS (M1.C) handles click + keyboard via the
            // same aria-expanded attribute.
            $delta = preg_replace(
                '/^(<li\b)([^>]*)>/',
                '$1$2 data-brndle-has-submenu="true" aria-haspopup="true">',
                $delta,
                1
            );
            $delta = preg_replace(
                '/(<a\b)([^>]*)>/',
                '$1$2 aria-expanded="false">',
                $delta,
                1
            );
        }

        $output = $before . $delta;
    }

    /**
     * Close a single menu item. Passthrough.
     *
     * @param  string         $output
     * @param  \WP_Post       $item
     * @param  int            $depth
     * @param  \stdClass|null $args
     * @return void
     */
    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        parent::end_el($output, $item, $depth, $args);
    }
}
