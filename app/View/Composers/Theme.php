<?php

/**
 * Theme view composer.
 *
 * Injects all Brndle theme settings into every Blade view so
 * templates can reference $darkModeDefault, $headerStyle, etc.
 * directly without manually resolving settings each time.
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

    // ── Dark Mode ────────────────────────────────────────────

    public function darkModeDefault(): string
    {
        return Settings::get('dark_mode_default', 'light');
    }

    public function showDarkModeToggle(): bool
    {
        return (bool) Settings::get('dark_mode_toggle', true);
    }

    public function darkModeTogglePosition(): string
    {
        return Settings::get('dark_mode_toggle_position', 'bottom-right');
    }

    // ── Header ───────────────────────────────────────────────

    public function headerStyle(): string
    {
        return Settings::get('header_style', 'sticky');
    }

    public function headerCtaText(): string
    {
        return Settings::get('header_cta_text', '');
    }

    public function headerCtaUrl(): string
    {
        return Settings::get('header_cta_url', '');
    }

    // ── Footer ───────────────────────────────────────────────

    public function footerStyle(): string
    {
        return Settings::get('footer_style', 'dark');
    }

    public function footerColumns(): int
    {
        return (int) Settings::get('footer_columns', 3);
    }

    public function footerCopyright(): string
    {
        $custom = Settings::get('footer_copyright', '');

        if (! empty($custom)) {
            return $custom;
        }

        $year = date('Y');
        $name = get_bloginfo('name', 'display');

        return "&copy; {$year} {$name}. All rights reserved.";
    }

    public function footerShowSocial(): bool
    {
        return (bool) Settings::get('footer_show_social', true);
    }

    // ── Archive ──────────────────────────────────────────────

    public function archiveLayout(): string
    {
        /** @var string */
        return apply_filters(
            'brndle/archive_layout',
            Settings::get('archive_layout', 'grid')
        );
    }

    public function archiveShowCategoryFilter(): bool
    {
        return (bool) Settings::get('archive_show_category_filter', true);
    }

    // ── Single Post ──────────────────────────────────────────

    public function singleLayout(): string
    {
        /** @var string */
        return apply_filters(
            'brndle/single_layout',
            Settings::get('single_layout', 'standard'),
            get_the_ID()
        );
    }

    public function singleShowProgressBar(): bool
    {
        return (bool) Settings::get('single_show_progress_bar', true);
    }

    public function singleShowReadingTime(): bool
    {
        return (bool) Settings::get('single_show_reading_time', true);
    }

    public function singleShowAuthorBox(): bool
    {
        return (bool) Settings::get('single_show_author_box', true);
    }

    public function singleShowSocialShare(): bool
    {
        return (bool) Settings::get('single_show_social_share', true);
    }

    public function singleShowRelatedPosts(): bool
    {
        return (bool) Settings::get('single_show_related_posts', true);
    }

    public function singleShowToc(): bool
    {
        return (bool) Settings::get('single_show_toc', false);
    }

    public function singleShowPostNav(): bool
    {
        return (bool) Settings::get('single_show_post_nav', true);
    }

    // ── Social Links ─────────────────────────────────────────

    /**
     * Return non-empty social links as platform => URL pairs.
     *
     * @return array<string, string>
     */
    public function socialLinks(): array
    {
        $links = Settings::get('social_links', []);

        if (! is_array($links)) {
            return [];
        }

        return array_filter($links, fn ($url) => ! empty($url));
    }
}
