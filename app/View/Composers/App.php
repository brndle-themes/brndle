<?php

namespace Brndle\View\Composers;

use Brndle\Settings\Settings;
use Roots\Acorn\View\Composer;

class App extends Composer
{
    protected static $views = ['*'];

    private static ?array $cachedData = null;

    public function override(): array
    {
        if (self::$cachedData !== null) {
            return self::$cachedData;
        }

        // First check Brndle settings, then fall back to WP custom logo
        $logoLight = Settings::get('site_logo_light');
        if (empty($logoLight)) {
            $logoId = get_theme_mod('custom_logo');
            $logoLight = $logoId ? wp_get_attachment_image_url($logoId, 'full') : null;
        }

        $logoDark = Settings::get('site_logo_dark');

        self::$cachedData = [
            'siteName'        => get_bloginfo('name', 'display'),
            'siteDescription' => get_bloginfo('description', 'display'),
            'siteLogo'        => $logoLight ?: null,
            'siteLogoDark'    => ! empty($logoDark) ? $logoDark : null,
        ];

        return self::$cachedData;
    }
}
