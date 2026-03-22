<?php

/**
 * Theme view composer.
 *
 * Injects all Brndle theme settings into every Blade view so
 * templates can reference $darkModeDefault, $headerStyle, etc.
 * directly without manually resolving settings each time.
 *
 * Uses with() instead of individual methods to return plain values
 * rather than InvokableComponentVariable wrappers. This ensures
 * boolean comparisons and string equality checks work correctly
 * in Blade templates.
 */

namespace Brndle\View\Composers;

use Brndle\Settings\Settings;
use Roots\Acorn\View\Composer;

class Theme extends Composer
{
    /**
     * Attach to every view.
     *
     * @var string[]
     */
    protected static $views = ['*'];

    /**
     * Data to pass to all views.
     *
     * @return array<string, mixed>
     */
    public function override(): array
    {
        $links = Settings::get('social_links', []);

        $copyright = Settings::get('footer_copyright', '');
        if (empty($copyright)) {
            $copyright = '&copy; ' . date('Y') . ' ' . get_bloginfo('name', 'display') . '. All rights reserved.';
        }

        return [
            // Dark Mode
            'darkModeDefault' => Settings::get('dark_mode_default', 'light'),
            'showDarkModeToggle' => (bool) Settings::get('dark_mode_toggle', true),
            'darkModeTogglePosition' => Settings::get('dark_mode_toggle_position', 'bottom-right'),

            // Header
            'headerStyle' => Settings::get('header_style', 'sticky'),
            'headerCtaText' => Settings::get('header_cta_text', ''),
            'headerCtaUrl' => Settings::get('header_cta_url', ''),
            'headerBannerText' => Settings::get('header_banner_text', 'Free shipping on all orders'),

            // Footer
            'footerStyle' => Settings::get('footer_style', 'dark'),
            'footerColumns' => (int) Settings::get('footer_columns', 3),
            'footerCopyright' => $copyright,
            'footerShowSocial' => (bool) Settings::get('footer_show_social', true),

            // Archive
            'archiveLayout' => apply_filters('brndle/archive_layout', Settings::get('archive_layout', 'grid')),
            'archiveShowCategoryFilter' => (bool) Settings::get('archive_show_category_filter', true),

            // Single Post
            'singleLayout' => apply_filters('brndle/single_layout', Settings::get('single_layout', 'standard'), get_the_ID()),
            'singleShowProgressBar' => (bool) Settings::get('single_show_progress_bar', true),
            'singleShowReadingTime' => (bool) Settings::get('single_show_reading_time', true),
            'singleShowAuthorBox' => (bool) Settings::get('single_show_author_box', true),
            'singleShowSocialShare' => (bool) Settings::get('single_show_social_share', true),
            'singleShowRelatedPosts' => (bool) Settings::get('single_show_related_posts', true),
            'singleShowToc' => (bool) Settings::get('single_show_toc', false),
            'singleShowPostNav' => (bool) Settings::get('single_show_post_nav', true),

            // Social Links
            'socialLinks' => is_array($links) ? array_filter($links, fn ($url) => ! empty($url)) : [],
        ];
    }
}

