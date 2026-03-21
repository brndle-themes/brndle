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
     * Register the Brndle settings page under Appearance.
     *
     * @return void
     */
    public function registerAdminPage(): void
    {
        add_theme_page(
            __('Brndle Settings', 'brndle'),
            __('Brndle', 'brndle'),
            'manage_options',
            'brndle-settings',
            [$this, 'renderAdminPage']
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
     * Output CSS custom properties in the document head.
     *
     * Hooked to wp_head at priority 1 so variables are available before
     * any theme stylesheet that references them.
     *
     * @return void
     */
    public function outputCssVariables(): void
    {
        $css = Settings::cssVariables();

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
     * Output font preloading tags in the document head.
     *
     * Emits preconnect hints, the Google Fonts stylesheet link using
     * the non-blocking print/onload pattern, and a noscript fallback.
     * Handles CDN fonts (e.g. Geist via jsDelivr) separately.
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
        $googleUrl = FontPairs::googleFontsUrl($fontPairKey);

        // Geist Sans is served via jsDelivr CDN, not Google Fonts.
        if ($fontPairKey === 'geist') {
            $geistUrl = 'https://cdn.jsdelivr.net/npm/geist@1/dist/fonts/geist-sans/style.css';

            echo '<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>' . "\n";
            echo '<link rel="stylesheet" href="' . esc_url($geistUrl) . '" media="print" onload="this.media=\'all\'">' . "\n";
            echo '<noscript><link rel="stylesheet" href="' . esc_url($geistUrl) . '"></noscript>' . "\n";

            return;
        }

        // System fonts need no loading.
        if ($fontPairKey === 'system' || $googleUrl === null) {
            return;
        }

        // Preconnect hints for Google Fonts.
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

        // Non-blocking font load with print/onload pattern.
        echo '<link rel="stylesheet" href="' . esc_url($googleUrl) . '" media="print" onload="this.media=\'all\'">' . "\n";

        // Noscript fallback for environments without JavaScript.
        echo '<noscript><link rel="stylesheet" href="' . esc_url($googleUrl) . '"></noscript>' . "\n";
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

        $perPage = (int) Settings::get('archive_posts_per_page', 12);
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
        if ($hookSuffix !== 'appearance_page_brndle-settings') {
            return;
        }

        $assetFile = get_theme_file_path('admin/build/index.asset.php');
        $asset = file_exists($assetFile)
            ? require $assetFile
            : ['dependencies' => [], 'version' => wp_get_theme()->get('Version')];

        wp_enqueue_script(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.js'),
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_localize_script('brndle-admin', 'brndleAdmin', [
            'restUrl' => rest_url('brndle/v1/'),
            'nonce'   => wp_create_nonce('wp_rest'),
        ]);

        wp_enqueue_style(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.css'),
            ['wp-components'],
            $asset['version']
        );
    }
}
