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

        // Compatibility — only boot when the plugin is active
        if (defined('WPSEO_VERSION')) {
            \Brndle\Compatibility\Yoast::boot();
        }
        if (class_exists('WooCommerce')) {
            \Brndle\Compatibility\WooCommerce::boot();
        }
        if (defined('ICL_SITEPRESS_VERSION')) {
            \Brndle\Compatibility\WPML::boot();
        }

        // Invalidate category filter cache when categories change
        $clearCatCache = fn () => delete_transient('brndle_top_categories');
        add_action('created_category', $clearCatCache);
        add_action('edited_category', $clearCatCache);
        add_action('delete_category', $clearCatCache);

        // Onboarding
        \Brndle\Onboarding\SetupNotice::boot();
        \Brndle\Onboarding\StarterContent::boot();
    }
}
