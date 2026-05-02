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
            'perf_view_transitions' => false,
            'perf_critical_css' => false,

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
