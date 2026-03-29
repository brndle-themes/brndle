<?php

namespace Brndle\Providers;

use Brndle\Settings\ColorPalette;
use Brndle\Settings\Settings;

class PageMetaServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerMeta']);
        add_filter('body_class', [$this, 'addBodyClass']);
        add_action('wp_head', [$this, 'outputPageCss'], 5);
        add_action('wp_head', [$this, 'outputColorSchemeOverride'], 3);
        add_action('add_meta_boxes', [$this, 'registerMetaBox']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueMetaBoxAssets']);
    }

    /**
     * Register the meta box for the block editor.
     */
    public function registerMetaBox(): void
    {
        add_meta_box(
            'brndle-page-settings',
            __('Brndle Page Settings', 'brndle'),
            [$this, 'renderMetaBox'],
            'page',
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box container for React mount.
     */
    public function renderMetaBox(\WP_Post $post): void
    {
        echo '<div id="brndle-page-settings-root"></div>';
    }

    /**
     * Enqueue the page settings meta box script in the block editor.
     */
    public function enqueueMetaBoxAssets(): void
    {
        $screen = get_current_screen();

        if (! $screen || $screen->post_type !== 'page') {
            return;
        }

        $assetFile = get_theme_file_path('blocks/build/page-meta-sidebar.asset.php');
        $asset = file_exists($assetFile)
            ? require $assetFile
            : ['dependencies' => ['wp-element', 'wp-components', 'wp-data', 'wp-dom-ready'], 'version' => '1.0.0'];

        wp_enqueue_script(
            'brndle-page-meta',
            get_theme_file_uri('blocks/build/page-meta-sidebar.js'),
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_enqueue_style(
            'brndle-page-meta',
            get_theme_file_uri('blocks/build/page-meta-sidebar.css'),
            ['wp-components'],
            $asset['version']
        );
    }

    public function addBodyClass(array $classes): array
    {
        if (is_singular('page')) {
            $extra = get_post_meta(get_the_ID(), '_brndle_body_class', true);
            if ($extra) {
                $classes = array_merge($classes, array_filter(explode(' ', $extra)));
            }

            if ((bool) get_post_meta(get_the_ID(), '_brndle_hide_title', true)) {
                $classes[] = 'brndle-hide-title';
            }

            $width = get_post_meta(get_the_ID(), '_brndle_content_width', true);
            if ($width && $width !== 'default') {
                $classes[] = 'brndle-content-' . $width;
            }
        }

        return $classes;
    }

    /**
     * Output per-page custom CSS in the document head.
     */
    public function outputPageCss(): void
    {
        if (! is_singular('page')) {
            return;
        }

        $css = get_post_meta(get_the_ID(), '_brndle_custom_css', true);

        if (empty($css)) {
            return;
        }

        $css = wp_strip_all_tags($css);
        $css = str_replace(['</style>', '</STYLE>'], '', $css);

        echo '<style id="brndle-page-css">' . $css . '</style>' . "\n";
    }

    /**
     * Output per-page color scheme CSS variable overrides.
     */
    public function outputColorSchemeOverride(): void
    {
        if (! is_singular('page')) {
            return;
        }

        $scheme = get_post_meta(get_the_ID(), '_brndle_color_scheme', true);

        if (empty($scheme)) {
            return;
        }

        $globalScheme = Settings::get('color_scheme', 'sapphire');

        if ($scheme === $globalScheme) {
            return;
        }

        $presets = ColorPalette::presets();

        if (! isset($presets[$scheme])) {
            return;
        }

        $palette = ColorPalette::generate($presets[$scheme]['hex']);

        $vars = [
            '--color-accent' => $palette['accent'],
            '--color-accent-hover' => $palette['accent-hover'],
            '--color-accent-light' => $palette['accent-light'],
            '--color-accent-subtle' => $palette['accent-subtle'],
            '--color-on-accent' => $palette['on-accent'],
        ];

        $css = ':root{';
        foreach ($vars as $prop => $val) {
            $css .= $prop . ':' . $val . ';';
        }
        $css .= '}';

        echo '<style id="brndle-page-scheme">' . $css . '</style>' . "\n";
    }

    public function registerMeta(): void
    {
        $headerStyles = ['', 'sticky', 'solid', 'transparent', 'centered', 'minimal', 'split', 'banner', 'glass'];
        $footerStyles = ['', 'dark', 'light', 'columns', 'minimal', 'big', 'stacked'];
        $colorSchemes = ['', 'sapphire', 'indigo', 'cobalt', 'trust', 'commerce', 'signal', 'coral', 'aubergine', 'midnight', 'stone', 'carbon', 'neutral'];
        $contentWidths = ['', 'default', 'narrow', 'wide', 'full'];

        $meta_fields = [
            '_brndle_header_style' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $headerStyles, true) ? $v : '',
            ],
            '_brndle_footer_style' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $footerStyles, true) ? $v : '',
            ],
            '_brndle_hide_header' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_hide_footer' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_hide_title' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_color_scheme' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $colorSchemes, true) ? $v : '',
            ],
            '_brndle_content_width' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $contentWidths, true) ? $v : '',
            ],
            '_brndle_body_class' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => implode(' ', array_map('sanitize_html_class', explode(' ', $v))),
            ],
            '_brndle_custom_css' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => wp_strip_all_tags($v),
            ],
        ];

        foreach ($meta_fields as $key => $args) {
            $registration = [
                'show_in_rest' => true,
                'single' => true,
                'type' => $args['type'],
                'default' => $args['default'],
                'auth_callback' => fn () => current_user_can('edit_posts'),
            ];

            if (isset($args['sanitize_callback'])) {
                $registration['sanitize_callback'] = $args['sanitize_callback'];
            }

            register_post_meta('page', $key, $registration);
        }
    }
}
