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

        // Admin UI: render the mega-config fields under each menu item
        // in the menu editor. WP 5.4+ fires `wp_nav_menu_item_custom_fields`
        // for every item the editor displays.
        add_action('wp_nav_menu_item_custom_fields', [self::class, 'renderFields'], 10, 5);

        // Enqueue admin media + small picker JS only on the menus screen.
        add_action('admin_enqueue_scripts', [self::class, 'enqueueMenuAssets']);
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
            // Content source for the mega panel: `manual` uses the menu's
            // child items as columns (default), `widget-area` swaps the
            // columns for a dynamic sidebar, `auto-posts` renders the
            // latest N posts from a chosen category as cards.
            '_brndle_mega_source' => [
                'type' => 'string',
                'sanitize' => static function ($v) {
                    $v = sanitize_text_field((string) $v);
                    return in_array($v, ['manual', 'widget-area', 'auto-posts'], true) ? $v : 'manual';
                },
            ],
            // Auto-posts: which category to pull from + how many posts to
            // show. Both ignored when source != auto-posts.
            '_brndle_mega_auto_category' => [
                'type' => 'integer',
                'sanitize' => static fn ($v) => absint($v),
            ],
            '_brndle_mega_auto_count' => [
                'type' => 'integer',
                'sanitize' => static fn ($v) => max(1, min(12, absint($v) ?: 6)),
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

    /**
     * Render the mega-menu admin fields under a single menu item in the
     * menu editor. Fired once per item by WordPress's
     * `wp_nav_menu_item_custom_fields` action (WP 5.4+).
     *
     * The form fields use a single `brndle_menu_meta[$itemId][$key]`
     * namespace so the save handler (self::save) can find them.
     *
     * Top-level-only fields (mega config, featured block, CTA) are still
     * rendered for child items but only the toggle is meaningful — the
     * other fields apply to top-level. The walker ignores them on children.
     *
     * @param  int      $itemId          Menu item post ID.
     * @param  \WP_Post $item            Menu item object.
     * @param  int      $depth           0 for top-level.
     * @param  mixed    $args            Menu walker args (unused).
     * @param  int      $currentObjectId Current object ID (unused).
     * @return void
     */
    public static function renderFields(int $itemId, $item, int $depth, $args, int $currentObjectId): void
    {
        $meta = self::get($itemId);

        // Lucide icons available on this site — read from resources/icons/.
        $iconsDir = get_theme_file_path('resources/icons');
        $availableIcons = [];
        if (is_dir($iconsDir)) {
            foreach (glob($iconsDir . '/*.svg') as $file) {
                $availableIcons[] = basename($file, '.svg');
            }
            sort($availableIcons);
        }

        $listId = 'brndle-icons-' . $itemId;

        ?>
        <fieldset class="field-brndle-mega description description-wide" style="border-top:1px solid #ddd;margin-top:16px;padding-top:12px;">
            <legend style="display:block;font-weight:600;margin-bottom:8px;color:#1d2327;">
                <?php esc_html_e('Brndle: Mega menu', 'brndle'); ?>
            </legend>

            <p>
                <label>
                    <input type="checkbox"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_menu]"
                           value="1"
                           <?php checked(! empty($meta['_brndle_mega_menu'])); ?>>
                    <?php esc_html_e('Display children as mega menu (only meaningful on top-level items)', 'brndle'); ?>
                </label>
            </p>

            <p class="description">
                <label>
                    <?php esc_html_e('Mega columns', 'brndle'); ?>
                    <select name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_columns]">
                        <?php foreach ([2, 3, 4] as $n): ?>
                            <option value="<?php echo (int) $n; ?>" <?php selected((int) $meta['_brndle_mega_columns'] ?: 3, $n); ?>>
                                <?php echo (int) $n; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>

            <p class="description">
                <label>
                    <?php esc_html_e('Content source', 'brndle'); ?>
                    <select name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_source]">
                        <?php
                        $currentSource = $meta['_brndle_mega_source'] ?: 'manual';
                        $sources = [
                            'manual' => __('Manual — use this menu item\'s children', 'brndle'),
                            'widget-area' => __('Widget area — drop in any WP widgets', 'brndle'),
                            'auto-posts' => __('Auto-posts — latest N from a category', 'brndle'),
                        ];
                        foreach ($sources as $val => $label): ?>
                            <option value="<?php echo esc_attr($val); ?>" <?php selected($currentSource, $val); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Auto-posts: category', 'brndle'); ?>
                    <?php
                    wp_dropdown_categories([
                        'name' => 'brndle_menu_meta[' . esc_attr((string) $itemId) . '][_brndle_mega_auto_category]',
                        'selected' => (int) $meta['_brndle_mega_auto_category'],
                        'show_option_none' => __('— select a category —', 'brndle'),
                        'option_none_value' => '0',
                        'hide_empty' => false,
                        'hierarchical' => true,
                        'orderby' => 'name',
                    ]);
                    ?>
                </label>
            </p>

            <p class="description">
                <label>
                    <?php esc_html_e('Auto-posts: count (1-12)', 'brndle'); ?>
                    <input type="number"
                           min="1" max="12"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_auto_count]"
                           value="<?php echo esc_attr((string) ($meta['_brndle_mega_auto_count'] ?: 6)); ?>"
                           class="widefat code">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Featured image (attachment ID)', 'brndle'); ?>
                    <input type="number"
                           min="0"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_featured_image]"
                           value="<?php echo esc_attr((string) ($meta['_brndle_mega_featured_image'] ?? 0)); ?>"
                           class="widefat code">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Featured heading', 'brndle'); ?>
                    <input type="text"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_featured_heading]"
                           value="<?php echo esc_attr((string) $meta['_brndle_mega_featured_heading']); ?>"
                           class="widefat">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Featured description', 'brndle'); ?>
                    <input type="text"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_featured_description]"
                           value="<?php echo esc_attr((string) $meta['_brndle_mega_featured_description']); ?>"
                           class="widefat">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Featured URL', 'brndle'); ?>
                    <input type="url"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_featured_url]"
                           value="<?php echo esc_attr((string) $meta['_brndle_mega_featured_url']); ?>"
                           class="widefat code">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Bottom CTA text', 'brndle'); ?>
                    <input type="text"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_cta_text]"
                           value="<?php echo esc_attr((string) $meta['_brndle_mega_cta_text']); ?>"
                           class="widefat">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Bottom CTA URL', 'brndle'); ?>
                    <input type="url"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_mega_cta_url]"
                           value="<?php echo esc_attr((string) $meta['_brndle_mega_cta_url']); ?>"
                           class="widefat code">
                </label>
            </p>
        </fieldset>

        <fieldset class="field-brndle-item description description-wide" style="border-top:1px solid #ddd;margin-top:8px;padding-top:12px;">
            <legend style="display:block;font-weight:600;margin-bottom:8px;color:#1d2327;">
                <?php esc_html_e('Brndle: Item details', 'brndle'); ?>
            </legend>

            <p class="description">
                <label>
                    <?php esc_html_e('Column (1-4, blank for auto)', 'brndle'); ?>
                    <input type="number"
                           min="0" max="4"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_column]"
                           value="<?php echo esc_attr((string) ($meta['_brndle_column'] ?: '')); ?>"
                           class="widefat code">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Column heading (renders this item as a section title instead of a link)', 'brndle'); ?>
                    <input type="text"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_column_heading]"
                           value="<?php echo esc_attr((string) $meta['_brndle_column_heading']); ?>"
                           class="widefat">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Icon (Lucide name)', 'brndle'); ?>
                    <input type="text"
                           list="<?php echo esc_attr($listId); ?>"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_icon]"
                           value="<?php echo esc_attr((string) $meta['_brndle_icon']); ?>"
                           class="widefat code"
                           placeholder="rocket">
                    <datalist id="<?php echo esc_attr($listId); ?>">
                        <?php foreach ($availableIcons as $iconName): ?>
                            <option value="<?php echo esc_attr($iconName); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Description (1 short line)', 'brndle'); ?>
                    <input type="text"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_description]"
                           value="<?php echo esc_attr((string) $meta['_brndle_description']); ?>"
                           class="widefat">
                </label>
            </p>

            <p class="description description-wide">
                <label>
                    <?php esc_html_e('Badge (e.g. NEW / BETA / PRO — max 6 chars)', 'brndle'); ?>
                    <input type="text"
                           maxlength="6"
                           name="brndle_menu_meta[<?php echo esc_attr((string) $itemId); ?>][_brndle_badge]"
                           value="<?php echo esc_attr((string) $meta['_brndle_badge']); ?>"
                           class="widefat code">
                </label>
            </p>
        </fieldset>
        <?php
    }

    /**
     * Enqueue minimal styles on the WP menu editor screen so the brndle
     * fieldsets read consistently with the rest of the menu form.
     *
     * @param  string $hook
     * @return void
     */
    public static function enqueueMenuAssets(string $hook): void
    {
        if ($hook !== 'nav-menus.php') {
            return;
        }

        $css = '
            .field-brndle-mega legend, .field-brndle-item legend {
                font-size: 13px;
                color: #1d2327;
            }
            .field-brndle-mega input[type="text"],
            .field-brndle-mega input[type="url"],
            .field-brndle-mega input[type="number"],
            .field-brndle-item input[type="text"],
            .field-brndle-item input[type="number"] {
                margin-top: 4px;
            }
        ';
        wp_add_inline_style('nav-menus', $css);
    }
}
