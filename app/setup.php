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
add_filter('should_load_separate_core_block_assets', '__return_true');

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
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
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
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Custom image sizes for blog
    add_image_size('brndle-card', 600, 400, true);
    add_image_size('brndle-hero', 1920, 1080, false);

    // Disable unused default sizes — theme only uses thumbnail, medium_large, brndle-card, brndle-hero
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
}, 20);

// Set image size options once on theme activation (not every request)
add_action('after_switch_theme', function () {
    update_option('medium_size_w', 0);
    update_option('medium_size_h', 0);
    update_option('large_size_w', 0);
    update_option('large_size_h', 0);
});

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

    // Remove global styles on landing pages (always) or all pages (opt-in setting)
    $removeGlobalStyles = is_page_template('template-landing')
        || \Brndle\Settings\Settings::get('perf_remove_global_styles', false);

    if ($removeGlobalStyles) {
        wp_dequeue_style('global-styles');
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

/**
 * Register page templates.
 */
add_filter('theme_page_templates', function ($templates) {
    $templates['template-landing'] = __('Landing Page', 'brndle');
    $templates['template-canvas'] = __('Full Canvas', 'brndle');
    $templates['template-transparent'] = __('Transparent Header', 'brndle');
    return $templates;
});
