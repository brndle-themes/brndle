<?php

/**
 * Settings service provider.
 *
 * Registers the admin settings page, REST API endpoints, CSS variable
 * injection, font preloading, and archive pagination for the Brndle theme.
 */

namespace Brndle\Providers;

use Brndle\Settings\Defaults;
use Brndle\Settings\FontPairs;
use Brndle\Settings\Sanitizer;
use Brndle\Settings\Settings;
use WP_REST_Request;
use WP_REST_Response;

class SettingsServiceProvider
{
    /**
     * Bootstrap all settings-related hooks.
     *
     * Registers admin menu, REST routes, CSS output, font preloading,
     * posts-per-page override, settings registration, and admin scripts.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerAdminPage']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('wp_head', [$this, 'outputCssVariables'], 1);
        add_filter('block_editor_settings_all', [$this, 'injectEditorCssVariables'], 20);
        add_action('wp_head', [$this, 'outputFontPreloads'], 2);
        add_action('pre_get_posts', [$this, 'setPostsPerPage']);
        add_action('admin_init', [$this, 'registerSetting']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * Register the Brndle settings page as a top-level admin menu.
     *
     * @return void
     */
    public function registerAdminPage(): void
    {
        add_menu_page(
            __('Brndle Settings', 'brndle'),
            __('Brndle', 'brndle'),
            'manage_options',
            'brndle-settings',
            [$this, 'renderAdminPage'],
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none"><path d="M4 2h7a5 5 0 0 1 3.5 1.5A4.5 4.5 0 0 1 16 7a4 4 0 0 1-1.2 2.8A5 5 0 0 1 16.5 14a4.5 4.5 0 0 1-1.5 3.5A5 5 0 0 1 12 18H4V2Zm3 2.5v4.5h4a2.5 2.5 0 0 0 2.5-2.2A2.5 2.5 0 0 0 11 4.5H7Zm0 7v4h5a2.5 2.5 0 0 0 2.5-2 2.5 2.5 0 0 0-2.5-2H7Z" fill="black"/></svg>'),
            59 // Position: after Comments (25), before Appearance (60)
        );
    }

    /**
     * Render the admin settings page markup.
     *
     * Outputs a single root div that the React admin app mounts into.
     *
     * @return void
     */
    public function renderAdminPage(): void
    {
        echo '<div id="brndle-settings-root"></div>';
    }

    /**
     * Register all REST API routes under the brndle/v1 namespace.
     *
     * @return void
     */
    public function registerRestRoutes(): void
    {
        $namespace = 'brndle/v1';

        register_rest_route($namespace, '/settings', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'handleGetSettings'],
                'permission_callback' => [$this, 'checkManageOptions'],
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'handleSaveSettings'],
                'permission_callback' => [$this, 'checkManageOptions'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$this, 'handleResetSettings'],
                'permission_callback' => [$this, 'checkManageOptions'],
            ],
        ]);

        register_rest_route($namespace, '/settings/export', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleExportSettings'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);

        register_rest_route($namespace, '/settings/import', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleImportSettings'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);

        register_rest_route($namespace, '/cache/purge', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handlePurgeCache'],
            'permission_callback' => [$this, 'checkManageOptions'],
        ]);
    }

    /**
     * Permission callback: require manage_options capability.
     *
     * @return bool
     */
    public function checkManageOptions(): bool
    {
        return current_user_can('manage_options');
    }

    /**
     * Handle GET /settings — return all current settings.
     *
     * @return WP_REST_Response
     */
    public function handleGetSettings(): WP_REST_Response
    {
        return new WP_REST_Response(Settings::all(), 200);
    }

    /**
     * Handle POST /settings — save partial or full settings.
     *
     * Sanitizes input, allowlists keys against known defaults, and
     * persists the result.
     *
     * @param  WP_REST_Request  $request
     * @return WP_REST_Response
     */
    public function handleSaveSettings(WP_REST_Request $request): WP_REST_Response
    {
        $input = $request->get_json_params();

        if (! is_array($input) || empty($input)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Invalid settings data.', 'brndle'),
            ], 400);
        }

        // Allowlist keys against known defaults.
        $allowedKeys = array_keys(Defaults::all());
        $filtered = array_intersect_key($input, array_flip($allowedKeys));

        if (empty($filtered)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('No valid settings keys provided.', 'brndle'),
            ], 400);
        }

        // Sanitize all values.
        $sanitized = Sanitizer::sanitizeAll($filtered);

        Settings::save($sanitized);

        return new WP_REST_Response([
            'success'  => true,
            'settings' => Settings::all(),
        ], 200);
    }

    /**
     * Handle DELETE /settings — reset all settings to defaults.
     *
     * @return WP_REST_Response
     */
    public function handleResetSettings(): WP_REST_Response
    {
        Settings::reset();

        return new WP_REST_Response([
            'success'  => true,
            'settings' => Defaults::all(),
        ], 200);
    }

    /**
     * Handle GET /settings/export — export settings as JSON.
     *
     * @return WP_REST_Response
     */
    public function handleExportSettings(): WP_REST_Response
    {
        return new WP_REST_Response(Settings::all(), 200);
    }

    /**
     * Handle POST /settings/import — import settings from JSON.
     *
     * Sanitizes all imported values before saving.
     *
     * @param  WP_REST_Request  $request
     * @return WP_REST_Response
     */
    public function handleImportSettings(WP_REST_Request $request): WP_REST_Response
    {
        $input = $request->get_json_params();

        if (! is_array($input) || empty($input)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('Invalid import data.', 'brndle'),
            ], 400);
        }

        // Allowlist keys against known defaults.
        $allowedKeys = array_keys(Defaults::all());
        $filtered = array_intersect_key($input, array_flip($allowedKeys));

        if (empty($filtered)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => __('No valid settings keys in import data.', 'brndle'),
            ], 400);
        }

        // Sanitize imported data.
        $sanitized = Sanitizer::sanitizeAll($filtered);

        Settings::save($sanitized);

        return new WP_REST_Response([
            'success'  => true,
            'settings' => Settings::all(),
        ], 200);
    }

    /**
     * Handle POST /cache/purge — clear all theme caches.
     *
     * Clears: compiled Blade views, in-memory settings cache,
     * and CSS variables static cache.
     *
     * @return WP_REST_Response
     */
    public function handlePurgeCache(): WP_REST_Response
    {
        $cleared = [];

        // 1. Clear compiled Blade views.
        $viewsPath = defined('WP_CONTENT_DIR')
            ? WP_CONTENT_DIR . '/cache/acorn/framework/views'
            : ABSPATH . 'wp-content/cache/acorn/framework/views';

        if (is_dir($viewsPath)) {
            $files = glob($viewsPath . '/*.php');
            $count = 0;

            if ($files) {
                foreach ($files as $file) {
                    if (unlink($file)) {
                        $count++;
                    }
                }
            }

            $cleared['blade_views'] = $count;
        } else {
            $cleared['blade_views'] = 0;
        }

        // 2. Clear in-memory settings cache.
        Settings::clearCache();
        $cleared['settings_cache'] = true;

        // 3. Flush WordPress object cache (picks up WP Rocket, Redis, etc.).
        wp_cache_flush();
        $cleared['object_cache'] = true;

        return new WP_REST_Response([
            'success' => true,
            'message' => __('All caches purged successfully.', 'brndle'),
            'cleared' => $cleared,
        ], 200);
    }

    /**
     * Output CSS custom properties in the document head.
     *
     * Hooked to wp_head at priority 1. The app.css dark mode fallback
     * lives inside @layer theme, so this unlayered output always wins
     * regardless of source order (CSS cascade: unlayered > layered).
     *
     * @return void
     */
    public function outputCssVariables(): void
    {
        $css = Settings::cssVariables();
        $css = str_replace(['</style>', '</STYLE>'], '', $css);

        echo '<style id="brndle-css-vars">' . $css . '</style>' . "\n";
    }

    /**
     * Inject CSS variables into the block editor.
     *
     * Appends the same CSS custom properties to the editor's inline
     * styles so blocks render with the correct theme colors.
     *
     * @param  array  $settings  Block editor settings array.
     * @return array  Modified settings.
     */
    public function injectEditorCssVariables(array $settings): array
    {
        $css = Settings::cssVariables();

        if (! isset($settings['styles'])) {
            $settings['styles'] = [];
        }

        $settings['styles'][] = [
            'css' => $css,
        ];

        return $settings;
    }

    /**
     * Output self-hosted font @font-face declarations in the document head.
     *
     * Generates inline @font-face CSS for the selected font pair using
     * local woff2 files bundled with the theme. Preloads the first font
     * file for optimal LCP performance.
     *
     * Hooked to wp_head at priority 2, immediately after CSS variables.
     *
     * @return void
     */
    public function outputFontPreloads(): void
    {
        if (! Settings::get('perf_preload_fonts', true)) {
            return;
        }

        $fontPairKey = Settings::get('font_pair', 'inter');
        $pairs = FontPairs::pairs();
        $pair = $pairs[$fontPairKey] ?? $pairs['inter'];

        // System fonts need no loading.
        if (empty($pair['fonts'])) {
            return;
        }

        $fontsDir = get_theme_file_uri('public/fonts');

        // Preload the primary font file for faster LCP.
        $primaryFont = $pair['fonts'][0];
        echo '<link rel="preload" as="font" type="font/woff2" href="' . esc_url($fontsDir . '/' . $primaryFont['file']) . '" crossorigin>' . "\n";

        // Generate @font-face declarations for all fonts in this pair.
        $css = '';
        foreach ($pair['fonts'] as $font) {
            $css .= '@font-face{';
            $css .= 'font-family:"' . $font['family'] . '";';
            $css .= 'src:url(' . esc_url($fontsDir . '/' . $font['file']) . ') format("woff2");';
            $css .= 'font-weight:' . $font['weight'] . ';';
            $css .= 'font-style:' . $font['style'] . ';';
            $css .= 'font-display:swap;';
            $css .= '}';
        }

        echo '<style id="brndle-fonts">' . $css . '</style>' . "\n";
    }

    /**
     * Set posts per page for archive queries.
     *
     * Only applies to the frontend main query on home and archive pages.
     * Reads the value from the archive_posts_per_page setting.
     *
     * @param  \WP_Query  $query
     * @return void
     */
    public function setPostsPerPage(\WP_Query $query): void
    {
        if (is_admin() || ! $query->is_main_query()) {
            return;
        }

        if (! $query->is_home() && ! $query->is_archive()) {
            return;
        }

        $perPage = max(1, min(100, (int) Settings::get('archive_posts_per_page', 12)));
        $query->set('posts_per_page', $perPage);
    }

    /**
     * Register the settings option with WordPress.
     *
     * @return void
     */
    public function registerSetting(): void
    {
        register_setting('brndle_settings_group', Settings::OPTION_KEY, [
            'type'         => 'object',
            'show_in_rest' => false,
        ]);
    }

    /**
     * Enqueue admin scripts and styles for the settings page.
     *
     * Only loads assets on the Brndle settings page. Uses the
     * auto-generated asset file for dependency and version management.
     *
     * @param  string  $hookSuffix  Current admin page hook suffix.
     * @return void
     */
    public function enqueueAdminAssets(string $hookSuffix): void
    {
        if ($hookSuffix !== 'toplevel_page_brndle-settings') {
            return;
        }

        $assetFile = get_theme_file_path('admin/build/index.asset.php');
        $asset = file_exists($assetFile)
            ? require $assetFile
            : ['dependencies' => [], 'version' => wp_get_theme()->get('Version')];

        // Enqueue WordPress media library for logo uploads
        wp_enqueue_media();

        wp_enqueue_script(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.js'),
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_localize_script('brndle-admin', 'brndleAdmin', [
            'restUrl'  => rest_url('brndle/v1/settings'),
            'cacheUrl' => rest_url('brndle/v1/cache/purge'),
            'nonce'    => wp_create_nonce('wp_rest'),
        ]);

        wp_enqueue_style(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.css'),
            ['wp-components'],
            $asset['version']
        );
    }
}
