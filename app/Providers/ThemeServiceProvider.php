<?php

namespace Brndle\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        $this->app->make(BlockServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\PageMetaServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\BlockPatternServiceProvider::class)->boot();
        $this->app->make(SettingsServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\FormServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\PerformanceServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\NavigationServiceProvider::class)->boot();
        $this->app->make(\Brndle\Providers\AvatarsServiceProvider::class)->boot();

        // Compatibility — only boot when the plugin is active
        if (defined('WPSEO_VERSION')) {
            \Brndle\Compatibility\Yoast::boot();
        }
        if (class_exists('RankMath')) {
            \Brndle\Compatibility\RankMath::boot();
        }
        \Brndle\Compatibility\SeoPluginNotice::boot();
        if (class_exists('WooCommerce')) {
            \Brndle\Compatibility\WooCommerce::boot();
        }
        if (defined('ICL_SITEPRESS_VERSION')) {
            \Brndle\Compatibility\WPML::boot();
        }

        // Invalidate category-derived caches when categories change. Two
        // transients to clear:
        //   - brndle_top_categories      → category filter pills cache
        //   - brndle_homepage_auto_cats  → sections layout auto-defaults cache
        //
        // Hook scope: ONLY category CRUD. We do not invalidate on per-post
        // save/transition because (a) the top-5 by count is stable on large
        // sites — the order rarely shifts even when individual posts
        // publish — and (b) on a 7k+ post site with active editors,
        // hooking save_post would clear the transient hundreds of times
        // per minute, defeating the cache. The 1h TTL handles natural
        // drift; an admin re-saving Brndle settings also clears it.
        $clearCatCache = function () {
            delete_transient('brndle_top_categories');
            delete_transient('brndle_homepage_auto_cats');
        };
        add_action('created_category', $clearCatCache);
        add_action('edited_category', $clearCatCache);
        add_action('delete_category', $clearCatCache);
        add_action('update_option_brndle_settings', $clearCatCache);

        // Onboarding
        \Brndle\Onboarding\SetupNotice::boot();
        \Brndle\Onboarding\StarterContent::boot();
    }
}
