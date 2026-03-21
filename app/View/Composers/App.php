<?php

namespace Brndle\View\Composers;

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
        $logo_id = get_theme_mod('custom_logo');

        return $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : null;
    }
}
