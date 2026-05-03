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
