<?php

/**
 * Theme setting defaults.
 *
 * Single source of truth for every Brndle setting default value.
 * Grouped by concern so the customizer/REST layer can iterate sections.
 */

namespace Brndle\Settings;

class Defaults
{
    /**
     * Return every default setting keyed by its option name.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $defaults = [
            // ── Site Identity ───────────────────────────────────
            'site_logo_light' => '',
            'site_logo_dark' => '',
            'social_links' => [
                'twitter' => '',
                'linkedin' => '',
                'github' => '',
                'instagram' => '',
            ],

            // ── Colors ──────────────────────────────────────────
            'color_scheme' => 'sapphire',
            'custom_accent' => '',

            // ── Dark Mode ───────────────────────────────────────
            'dark_mode_default' => 'light',
            'dark_mode_toggle' => true,
            'dark_mode_toggle_position' => 'bottom-right',

            // ── Typography ──────────────────────────────────────
            'font_pair' => 'inter',
            'font_size_base' => 16,
            'heading_scale' => 1.25,

            // ── Header ──────────────────────────────────────────
            'header_style' => 'sticky',
            'header_cta_text' => '',
            'header_cta_url' => '',
            'header_sticky_mode' => 'sticky-fixed',
            'header_search_enabled' => false,
            'header_banner_text' => 'Free shipping on all orders',
            'header_mobile_style' => 'slide',

            // ── Footer ──────────────────────────────────────────
            'footer_columns' => 3,
            'footer_copyright' => '',
            'footer_show_social' => true,
            'footer_style' => 'dark',

            // ── Blog Archive ────────────────────────────────────
            'archive_layout' => 'grid',
            'archive_posts_per_page' => 12,
            'archive_show_sidebar' => false,
            'archive_show_category_filter' => true,

            // ── Blog Homepage Sections (1.5.0) ───────────────────
            // Opt-in news-portal-style homepage. Activates only when the
            // blog is set as the site front page AND the toggle below is on.
            // See plans/2026-05-02-blog-homepage-sections.md for design.
            'homepage_sections_enabled' => false,
            'homepage_sections' => [],

            // ── Single Post ─────────────────────────────────────
            'single_layout' => 'standard',
            'single_show_progress_bar' => true,
            'single_show_reading_time' => true,
            'single_show_author_box' => true,
            'single_show_social_share' => true,
            'single_show_related_posts' => true,
            'single_show_toc' => false,
            'single_show_post_nav' => true,

            // ── Performance ─────────────────────────────────────
            'perf_remove_emoji' => true,
            'perf_remove_embed' => true,
            'perf_lazy_images' => true,
            'perf_preload_fonts' => true,
            'perf_remove_global_styles' => false,
            // 1.4.0 — default-on for pages / blogs. Existing sites that
            // already saved their settings keep their explicit values; the
            // flip only affects fresh installs and sites that have not yet
            // touched the admin Performance tab. Both features are safe on
            // older browsers (graceful fallback) and respect
            // `prefers-reduced-motion`.
            'perf_view_transitions' => true,
            'perf_critical_css' => true,

            // ── Forms & Integrations ───────────────────────────
            'mailchimp_api_key'        => '',
            'mailchimp_list_id'        => '',
            'form_webhook_url'         => '',
            'form_notification_email'  => '',
            'form_store_submissions'   => true,
            'form_email_notifications' => true,
        ];

        /** @var array<string, mixed> */
        return apply_filters('brndle/settings_defaults', $defaults);
    }

    /**
     * Field metadata for every Brndle setting.
     *
     * Single source of truth for the admin UI: section, label,
     * description, control type, and (where relevant) the option
     * choices or numeric range. The hand-coded admin tabs in
     * `admin/src/tabs/*.jsx` still ship today — this metadata is the
     * structural foundation that lets a future PR generate those
     * tabs from one place. Until that ships, the consistency CI
     * (`bin/check-settings-consistency.mjs`) verifies every key in
     * `all()` also has an entry here, so the two stay in sync.
     *
     * @return array<string, array{
     *     section: string,
     *     label: string,
     *     description?: string,
     *     control: 'text'|'textarea'|'url'|'email'|'color'|'image'|'select'|'toggle'|'number'|'range'|'social'|'html',
     *     options?: array<string, string>,
     *     min?: int|float,
     *     max?: int|float,
     *     step?: int|float,
     * }>
     */
    public static function schema(): array
    {
        $schema = [
            // ── Site Identity ───────────────────────────────────
            'site_logo_light' => ['section' => 'site-identity', 'label' => 'Logo (light)', 'description' => 'Shown in light mode and on light section variants.', 'control' => 'image'],
            'site_logo_dark' => ['section' => 'site-identity', 'label' => 'Logo (dark)', 'description' => 'Shown in dark mode and on dark section variants.', 'control' => 'image'],
            'social_links' => ['section' => 'site-identity', 'label' => 'Social profiles', 'control' => 'social'],

            // ── Colors ──────────────────────────────────────────
            'color_scheme' => ['section' => 'colors', 'label' => 'Color scheme', 'description' => 'One of 12 design-tested presets. Custom Accent overrides the preset accent if set.', 'control' => 'select'],
            'custom_accent' => ['section' => 'colors', 'label' => 'Custom accent', 'description' => 'Hex value that overrides the preset accent. Leave empty to use the preset.', 'control' => 'color'],

            // ── Dark Mode ───────────────────────────────────────
            'dark_mode_default' => ['section' => 'dark-mode', 'label' => 'Default mode', 'control' => 'select', 'options' => ['light' => 'Light', 'dark' => 'Dark', 'system' => 'Follow system']],
            'dark_mode_toggle' => ['section' => 'dark-mode', 'label' => 'Show toggle button', 'description' => 'Disabling drops the dark-mode JS controller entirely.', 'control' => 'toggle'],
            'dark_mode_toggle_position' => ['section' => 'dark-mode', 'label' => 'Toggle position', 'control' => 'select', 'options' => ['bottom-right' => 'Bottom right', 'bottom-left' => 'Bottom left', 'header' => 'Header']],

            // ── Typography ──────────────────────────────────────
            'font_pair' => ['section' => 'typography', 'label' => 'Font pair', 'description' => '8 self-hosted woff2 pairs.', 'control' => 'select'],
            'font_size_base' => ['section' => 'typography', 'label' => 'Base font size (px)', 'description' => 'Drives `html { font-size }` so every rem scales.', 'control' => 'range', 'min' => 12, 'max' => 24, 'step' => 1],
            'heading_scale' => ['section' => 'typography', 'label' => 'Heading scale ratio', 'description' => 'Geometric ratio between h6 and h1 inside `.prose` content. 1.25 ≈ Major Third.', 'control' => 'range', 'min' => 1.10, 'max' => 1.50, 'step' => 0.05],

            // ── Header ──────────────────────────────────────────
            'header_style' => ['section' => 'header', 'label' => 'Header style', 'description' => '8 layouts; pick the closest match for the brand.', 'control' => 'select'],
            'header_cta_text' => ['section' => 'header', 'label' => 'CTA button text', 'control' => 'text'],
            'header_cta_url' => ['section' => 'header', 'label' => 'CTA button URL', 'control' => 'url'],
            'header_banner_text' => ['section' => 'header', 'label' => 'Top banner text', 'description' => 'Optional one-line marketing message above the header.', 'control' => 'text'],
            'header_mobile_style' => ['section' => 'header', 'label' => 'Mobile menu style', 'control' => 'select', 'options' => ['slide' => 'Slide-in', 'fullscreen' => 'Fullscreen', 'dropdown' => 'Dropdown']],
            'header_sticky_mode' => ['section' => 'header', 'label' => 'Sticky mode', 'description' => 'How the header behaves on scroll.', 'control' => 'select', 'options' => ['static' => 'Static (no sticky)', 'sticky-fixed' => 'Sticky (always visible)', 'sticky-fade' => 'Sticky with fade-on-scroll', 'sticky-hide-on-scroll' => 'Hide on scroll down, reveal on scroll up']],
            'header_search_enabled' => ['section' => 'header', 'label' => 'Show header search', 'description' => 'Adds a search icon button to the header that opens a popover with the WordPress search form.', 'control' => 'toggle'],

            // ── Footer ──────────────────────────────────────────
            'footer_columns' => ['section' => 'footer', 'label' => 'Columns', 'control' => 'range', 'min' => 1, 'max' => 4, 'step' => 1],
            'footer_copyright' => ['section' => 'footer', 'label' => 'Copyright HTML', 'description' => 'Limited HTML allowed. Default uses site name + current year.', 'control' => 'html'],
            'footer_show_social' => ['section' => 'footer', 'label' => 'Show social links', 'control' => 'toggle'],
            'footer_style' => ['section' => 'footer', 'label' => 'Footer style', 'control' => 'select', 'options' => ['dark' => 'Dark', 'light' => 'Light', 'columns' => 'Columns', 'minimal' => 'Minimal', 'big' => 'Big', 'stacked' => 'Stacked']],

            // ── Blog Archive ────────────────────────────────────
            'archive_layout' => ['section' => 'blog-archive', 'label' => 'Archive layout', 'control' => 'select', 'options' => ['grid' => 'Grid', 'list' => 'List', 'magazine' => 'Magazine', 'editorial' => 'Editorial', 'minimal' => 'Minimal']],
            'archive_posts_per_page' => ['section' => 'blog-archive', 'label' => 'Posts per page', 'control' => 'range', 'min' => 1, 'max' => 100, 'step' => 1],
            'archive_show_sidebar' => ['section' => 'blog-archive', 'label' => 'Show sidebar', 'control' => 'toggle'],
            'archive_show_category_filter' => ['section' => 'blog-archive', 'label' => 'Show category filter', 'control' => 'toggle'],
            'homepage_sections_enabled' => ['section' => 'blog-homepage', 'label' => 'Use sections layout when blog is the homepage', 'control' => 'toggle', 'description' => 'When the blog is set as the front page, render stacked category sections (news-portal style) instead of a single archive layout.'],
            'homepage_sections' => ['section' => 'blog-homepage', 'label' => 'Homepage sections', 'control' => 'sections-builder', 'description' => 'Ordered list of category sections. Each section renders posts from one top-level category in the chosen visual style.'],

            // ── Single Post ─────────────────────────────────────
            'single_layout' => ['section' => 'single-post', 'label' => 'Single post layout', 'control' => 'select', 'options' => ['standard' => 'Standard', 'hero-immersive' => 'Hero Immersive', 'sidebar' => 'Sidebar', 'editorial' => 'Editorial', 'cinematic' => 'Cinematic', 'presentation' => 'Presentation', 'split' => 'Split', 'minimal-dark' => 'Minimal Dark']],
            'single_show_progress_bar' => ['section' => 'single-post', 'label' => 'Show reading progress bar', 'control' => 'toggle'],
            'single_show_reading_time' => ['section' => 'single-post', 'label' => 'Show reading time', 'control' => 'toggle'],
            'single_show_author_box' => ['section' => 'single-post', 'label' => 'Show author box', 'control' => 'toggle'],
            'single_show_social_share' => ['section' => 'single-post', 'label' => 'Show social share', 'control' => 'toggle'],
            'single_show_related_posts' => ['section' => 'single-post', 'label' => 'Show related posts', 'control' => 'toggle'],
            'single_show_toc' => ['section' => 'single-post', 'label' => 'Show table of contents', 'control' => 'toggle'],
            'single_show_post_nav' => ['section' => 'single-post', 'label' => 'Show post navigation', 'control' => 'toggle'],

            // ── Performance ─────────────────────────────────────
            'perf_remove_emoji' => ['section' => 'performance', 'label' => 'Remove emoji scripts', 'description' => 'Saves ~14 KiB on every page.', 'control' => 'toggle'],
            'perf_remove_embed' => ['section' => 'performance', 'label' => 'Remove wp-embed', 'control' => 'toggle'],
            'perf_lazy_images' => ['section' => 'performance', 'label' => 'Lazy-load images', 'control' => 'toggle'],
            'perf_preload_fonts' => ['section' => 'performance', 'label' => 'Preload primary font', 'control' => 'toggle'],
            'perf_remove_global_styles' => ['section' => 'performance', 'label' => 'Remove WP global styles on landing template', 'control' => 'toggle'],
            'perf_view_transitions' => ['section' => 'performance', 'label' => 'View Transitions (soft navigation)', 'description' => 'Adds ~2 KiB JS controller for SPA-feel navigation.', 'control' => 'toggle'],
            'perf_critical_css' => ['section' => 'performance', 'label' => 'Inline critical CSS, defer app.css', 'description' => 'Trade brief flash for faster first paint.', 'control' => 'toggle'],

            // ── Forms & Integrations ───────────────────────────
            'mailchimp_api_key' => ['section' => 'forms', 'label' => 'Mailchimp API key', 'control' => 'text'],
            'mailchimp_list_id' => ['section' => 'forms', 'label' => 'Mailchimp list / audience ID', 'control' => 'text'],
            'form_webhook_url' => ['section' => 'forms', 'label' => 'Webhook URL', 'description' => 'Optional POST endpoint that receives every submission.', 'control' => 'url'],
            'form_notification_email' => ['section' => 'forms', 'label' => 'Notification email', 'control' => 'email'],
            'form_store_submissions' => ['section' => 'forms', 'label' => 'Store submissions in DB', 'control' => 'toggle'],
            'form_email_notifications' => ['section' => 'forms', 'label' => 'Email notification', 'control' => 'toggle'],
        ];

        /** @var array<string, array<string, mixed>> */
        return apply_filters('brndle/settings_schema', $schema);
    }

    /**
     * Return the default value for a single key.
     *
     * @param  string  $key      Setting key name.
     * @param  mixed   $fallback Value when the key does not exist in defaults.
     * @return mixed
     */
    public static function get(string $key, mixed $fallback = null): mixed
    {
        return self::all()[$key] ?? $fallback;
    }

    /**
     * Keys that hold boolean values.
     *
     * @return string[]
     */
    public static function boolKeys(): array
    {
        return [
            'dark_mode_toggle',
            'footer_show_social',
            'archive_show_sidebar',
            'archive_show_category_filter',
            'single_show_progress_bar',
            'single_show_reading_time',
            'single_show_author_box',
            'single_show_social_share',
            'single_show_related_posts',
            'single_show_toc',
            'single_show_post_nav',
            'perf_remove_emoji',
            'perf_remove_embed',
            'perf_lazy_images',
            'perf_preload_fonts',
            'perf_remove_global_styles',
            'perf_view_transitions',
            'perf_critical_css',
            'homepage_sections_enabled',
            'header_search_enabled',
            'form_store_submissions',
            'form_email_notifications',
        ];
    }

    /**
     * Keys that hold integer values.
     *
     * @return string[]
     */
    public static function intKeys(): array
    {
        return [
            'font_size_base',
            'footer_columns',
            'archive_posts_per_page',
        ];
    }

    /**
     * Keys that hold color hex values.
     *
     * @return string[]
     */
    public static function colorKeys(): array
    {
        return [
            'custom_accent',
        ];
    }

    /**
     * Keys that hold URL values.
     *
     * @return string[]
     */
    public static function urlKeys(): array
    {
        return [
            'site_logo_light',
            'site_logo_dark',
            'header_cta_url',
            'form_webhook_url',
        ];
    }

    /**
     * Keys that hold email values.
     *
     * @return string[]
     */
    public static function emailKeys(): array
    {
        return [
            'form_notification_email',
        ];
    }

    /**
     * Keys that hold HTML content.
     *
     * @return string[]
     */
    public static function htmlKeys(): array
    {
        return [
            'footer_copyright',
        ];
    }

    /**
     * Keys that hold array values.
     *
     * @return string[]
     */
    public static function arrayKeys(): array
    {
        return [
            'social_links',
            'homepage_sections',
        ];
    }

    /**
     * Allowed style keys for a homepage section.
     *
     * Mirrors the seven Blade partials in
     * `resources/views/partials/sections-styles/`.
     *
     * @return string[]
     */
    public static function homepageSectionStyles(): array
    {
        return [
            'featured-hero',
            'grid-3col',
            'magazine-strip',
            'list-with-thumb',
            'mixed-2x2',
            'ticker',
            'editorial-pair',
        ];
    }

    /**
     * Keys that hold float values.
     *
     * @return string[]
     */
    public static function floatKeys(): array
    {
        return [
            'heading_scale',
        ];
    }
}
