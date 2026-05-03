<?php

/**
 * Nav menu item meta.
 *
 * Registers the `_brndle_*` post meta keys that drive mega-menu behavior
 * on individual nav menu items. M1.A ships the registration + save handler
 * only; the admin UI fields are added in a later M1 sub-chunk so this
 * scaffold is regression-free until the walker starts reading the meta.
 *
 * Storage layer: every nav menu item is a `nav_menu_item` post, so we use
 * post meta directly. WordPress's `wp_get_nav_menu_items()` primes post
 * meta cache automatically, which means reading these keys in the walker
 * costs no extra queries.
 *
 * Keys (all defined in self::keys()):
 *
 *   _brndle_mega_menu                 bool    only meaningful on depth=0 items
 *   _brndle_mega_columns              int     2-4
 *   _brndle_mega_featured_image       int     attachment ID
 *   _brndle_mega_featured_heading     string
 *   _brndle_mega_featured_description string
 *   _brndle_mega_featured_url         string
 *   _brndle_mega_cta_text             string
 *   _brndle_mega_cta_url              string
 *   _brndle_column                    int     1-4 (per child)
 *   _brndle_column_heading            string  if non-empty, item renders as <h4>
 *   _brndle_icon                      string  Lucide icon name (kebab-case)
 *   _brndle_description               string  1-line description
 *   _brndle_badge                     string  NEW / BETA / PRO / custom
 *
 * @see plans/2026-05-03-mega-menu.md
 */

namespace Brndle\Navigation;

class MenuItemMeta
{
    /**
     * Bootstrap meta registration + save handlers.
     *
     * @return void
     */
    public static function boot(): void
    {
        // Register every key for `nav_menu_item` posts. `register_post_meta`
        // makes the keys appear in REST + applies the sanitize callback on
        // save. We intentionally set `single => true` and `show_in_rest =>
        // false` (these are admin-only — no public REST exposure).
        add_action('init', [self::class, 'registerMeta']);

        // Save handler — fires from the menu editor when an item is updated.
        add_action('wp_update_nav_menu_item', [self::class, 'save'], 10, 3);
    }

    /**
     * Meta key list with their type + sanitize callback.
     *
     * Returning the list as a method (not a const) so other classes can
     * reference it for the admin UI without duplicating the schema.
     *
     * @return array<string, array{type:string, sanitize:callable}>
     */
    public static function keys(): array
    {
        return [
            '_brndle_mega_menu' => [
                'type' => 'boolean',
                'sanitize' => static fn ($v) => (bool) $v,
            ],
            '_brndle_mega_columns' => [
                'type' => 'integer',
                'sanitize' => static fn ($v) => max(2, min(4, (int) $v)),
            ],
            '_brndle_mega_featured_image' => [
                'type' => 'integer',
                'sanitize' => static fn ($v) => absint($v),
            ],
            '_brndle_mega_featured_heading' => [
                'type' => 'string',
                'sanitize' => 'sanitize_text_field',
            ],
            '_brndle_mega_featured_description' => [
                'type' => 'string',
                'sanitize' => 'sanitize_text_field',
            ],
            '_brndle_mega_featured_url' => [
                'type' => 'string',
                'sanitize' => 'esc_url_raw',
            ],
            '_brndle_mega_cta_text' => [
                'type' => 'string',
                'sanitize' => 'sanitize_text_field',
            ],
            '_brndle_mega_cta_url' => [
                'type' => 'string',
                'sanitize' => 'esc_url_raw',
            ],
            '_brndle_column' => [
                'type' => 'integer',
                'sanitize' => static fn ($v) => max(0, min(4, (int) $v)),
            ],
            '_brndle_column_heading' => [
                'type' => 'string',
                'sanitize' => 'sanitize_text_field',
            ],
            '_brndle_icon' => [
                'type' => 'string',
                'sanitize' => static fn ($v) => preg_replace('/[^a-z0-9-]/', '', strtolower((string) $v)),
            ],
            '_brndle_description' => [
                'type' => 'string',
                'sanitize' => 'sanitize_text_field',
            ],
            '_brndle_badge' => [
                'type' => 'string',
                'sanitize' => static function ($v) {
                    $v = sanitize_text_field((string) $v);
                    return mb_substr($v, 0, 6); // hard cap at 6 chars
                },
            ],
        ];
    }

    /**
     * Register all meta keys with WordPress.
     *
     * @return void
     */
    public static function registerMeta(): void
    {
        foreach (self::keys() as $key => $config) {
            register_post_meta('nav_menu_item', $key, [
                'type' => $config['type'],
                'single' => true,
                'show_in_rest' => false,
                'sanitize_callback' => $config['sanitize'],
                'auth_callback' => static fn () => current_user_can('edit_theme_options'),
            ]);
        }
    }

    /**
     * Save handler — fires when a menu item is updated in the menu editor.
     *
     * Reads from `$_POST` (the menu editor's form), sanitizes, and
     * persists. WordPress core already verifies the menu nonce before
     * firing this hook, so we rely on that — see `wp-admin/nav-menus.php`.
     *
     * @param  int   $menuId       Menu being saved (unused).
     * @param  int   $menuItemId   Menu item post ID.
     * @param  array $args         Menu item args (unused).
     * @return void
     */
    public static function save(int $menuId, int $menuItemId, array $args): void
    {
        if (! current_user_can('edit_theme_options')) {
            return;
        }

        // Phase 1.A: meta registration is in place but the admin UI fields
        // ship in a later sub-chunk. Until then this handler reads any
        // values that may already be POSTed from a future admin form, so the
        // save path is functional once the UI lands.
        foreach (self::keys() as $key => $config) {
            $postKey = 'brndle_menu_meta';
            if (! isset($_POST[$postKey][$menuItemId][$key])) {
                continue;
            }

            $raw = $_POST[$postKey][$menuItemId][$key]; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- core verifies the menu nonce before firing this hook
            $value = call_user_func($config['sanitize'], wp_unslash($raw));

            if ($value === '' || $value === null || $value === false || $value === 0) {
                delete_post_meta($menuItemId, $key);
            } else {
                update_post_meta($menuItemId, $key, $value);
            }
        }
    }

    /**
     * Read all `_brndle_*` meta values for a menu item. Returns sanitized
     * defaults for missing keys so consumers don't need null guards.
     *
     * @param  int $menuItemId
     * @return array<string, mixed>
     */
    public static function get(int $menuItemId): array
    {
        $out = [];
        foreach (self::keys() as $key => $config) {
            $value = get_post_meta($menuItemId, $key, true);
            if ($value === '' || $value === false) {
                $out[$key] = $config['type'] === 'boolean' ? false
                    : ($config['type'] === 'integer' ? 0 : '');
                continue;
            }
            $out[$key] = $value;
        }
        return $out;
    }
}
