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
        $this->app->make(SettingsServiceProvider::class)->boot();

        // Compatibility
        \Brndle\Compatibility\Yoast::boot();
        \Brndle\Compatibility\WooCommerce::boot();
        \Brndle\Compatibility\WPML::boot();

        // Onboarding
        \Brndle\Onboarding\SetupNotice::boot();
        \Brndle\Onboarding\StarterContent::boot();
    }
}
