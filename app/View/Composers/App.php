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

        // A square icon-mark carries no brand NAME, so the header must still
        // print the site title beside it. A wide lockup already contains the
        // wordmark, and printing the name again would duplicate it. Decide by
        // aspect ratio rather than asking the site owner to declare it.
        $logoIsLockup = false;
        if ($logoLight) {
            $logoMetaId = attachment_url_to_postid($logoLight);
            $logoMeta = $logoMetaId ? wp_get_attachment_metadata($logoMetaId) : null;
            if (! empty($logoMeta['width']) && ! empty($logoMeta['height'])) {
                $logoIsLockup = ($logoMeta['width'] / max(1, $logoMeta['height'])) >= 2.5;
            }
        }

        self::$cachedData = [
            'siteName'        => esc_html(get_bloginfo('name', 'display')),
            'siteDescription' => get_bloginfo('description', 'display'),
            'siteLogo'        => $logoLight ?: null,
            'siteLogoDark'    => ! empty($logoDark) ? $logoDark : null,
            'siteLogoIsLockup' => $logoIsLockup,
        ];

        return self::$cachedData;
    }
}
