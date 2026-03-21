<?php

/**
 * Theme setup.
 */

namespace Brndle;

use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 */
add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'));

        foreach ($dependencies as $dependency) {
            if (! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Disable on-demand block asset loading.
 */
add_filter('should_load_separate_core_block_assets', '__return_false');

/**
 * Register the initial theme setup.
 */
add_action('after_setup_theme', function () {
    // Disable full-site editing
    remove_theme_support('block-templates');

    // Navigation menus
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'brndle'),
        'footer_navigation' => __('Footer Navigation', 'brndle'),
        'footer_col_1' => __('Footer Column 1', 'brndle'),
        'footer_col_2' => __('Footer Column 2', 'brndle'),
        'footer_col_3' => __('Footer Column 3', 'brndle'),
    ]);

    // Disable default block patterns
    remove_theme_support('core-block-patterns');

    // Standard theme supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);
    add_theme_support('customize-selective-refresh-widgets');

    // Custom image sizes for blog
    add_image_size('brndle-card', 600, 400, true);
    add_image_size('brndle-hero', 1920, 1080, false);
}, 20);

/**
 * Register the theme sidebars.
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Footer', 'brndle'),
        'id' => 'sidebar-footer',
    ] + $config);
});

/**
 * Remove WordPress bloat for performance (conditional on settings).
 */
add_action('wp_enqueue_scripts', function () {
    if (\Brndle\Settings\Settings::get('perf_remove_emoji', true)) {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
    }

    if (\Brndle\Settings\Settings::get('perf_remove_embed', true)) {
        wp_dequeue_script('wp-embed');
    }

    // Remove global styles (block library CSS) on landing pages
    if (is_page_template('template-landing')) {
        wp_dequeue_style('global-styles');
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
    }
});

// Conditional lazy loading
add_filter('wp_lazy_loading_enabled', function () {
    return (bool) \Brndle\Settings\Settings::get('perf_lazy_images', true);
});

// Remove oEmbed discovery
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// Remove REST API link in head
remove_action('wp_head', 'rest_output_link_wp_head');

// Remove shortlink
remove_action('wp_head', 'wp_shortlink_wp_head');

// Remove WP generator meta
remove_action('wp_head', 'wp_generator');

// Remove RSD/wlwmanifest
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
