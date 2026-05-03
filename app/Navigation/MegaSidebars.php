<?php

/**
 * Mega menu widget-area sidebar registrar.
 *
 * For every nav menu item with `_brndle_mega_source = widget-area`, register
 * a uniquely-named sidebar (`brndle-mega-{menu_item_id}`) so the WP admin
 * Widgets / Customizer can drop widgets into it. The walker calls
 * `dynamic_sidebar()` on this name when rendering that mega panel.
 *
 * Why per-item sidebars instead of one shared one: site owners typically
 * want different widgets in different mega panels (e.g. mini-cart in one,
 * recent posts in another). Per-item sidebars give each panel its own
 * widget palette without forcing JS-driven conditional widget visibility.
 *
 * @see plans/2026-05-03-mega-menu.md (M2.D)
 */

namespace Brndle\Navigation;

class MegaSidebars
{
    /**
     * Bootstrap: register sidebars on `widgets_init`.
     *
     * @return void
     */
    public static function boot(): void
    {
        add_action('widgets_init', [self::class, 'registerSidebars'], 20);
    }

    /**
     * Query every nav_menu_item that has both `_brndle_mega_menu = 1` AND
     * `_brndle_mega_source = widget-area`, then register a sidebar for each.
     *
     * Runs at `widgets_init` priority 20, after most plugins register their
     * own. Single meta_query with two clauses, indexed lookup — fast even on
     * sites with hundreds of menu items.
     *
     * @return void
     */
    public static function registerSidebars(): void
    {
        $items = get_posts([
            'post_type' => 'nav_menu_item',
            'numberposts' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => '_brndle_mega_menu', 'value' => '1', 'compare' => '='],
                ['key' => '_brndle_mega_source', 'value' => 'widget-area', 'compare' => '='],
            ],
            'no_found_rows' => true,
            'update_post_term_cache' => false,
        ]);

        foreach ($items as $item) {
            register_sidebar([
                'id' => 'brndle-mega-' . $item->ID,
                'name' => sprintf(
                    /* translators: %s is the menu item title */
                    __('Mega menu — %s', 'brndle'),
                    get_the_title($item) ?: ('item ' . $item->ID)
                ),
                'description' => __('Widgets shown inside this menu item\'s mega panel.', 'brndle'),
                'before_widget' => '<div class="brndle-mega__widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h4 class="brndle-mega__widget-title">',
                'after_title' => '</h4>',
            ]);
        }
    }
}
