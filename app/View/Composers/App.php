<?php

namespace Brndle\View\Composers;

use Brndle\Settings\Settings;
use Roots\Acorn\View\Composer;

class App extends Composer
{
    protected static $views = ['*'];

    public function siteName(): string
    {
        return get_bloginfo('name', 'display');
    }

    public function siteDescription(): string
    {
        return get_bloginfo('description', 'display');
    }

    public function siteLogo(): ?string
    {
        // First check Brndle settings, then fall back to WP custom logo
        $url = Settings::get('site_logo_light');
        if (! empty($url)) {
            return $url;
        }

        $logo_id = get_theme_mod('custom_logo');
        return $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : null;
    }

    public function siteLogoDark(): ?string
    {
        $url = Settings::get('site_logo_dark');
        return ! empty($url) ? $url : null;
    }
}
