<?php

/**
 * Navigation service provider.
 *
 * Boots the mega-menu subsystem: nav menu item meta registration, the
 * admin save handler, and (later in M1) the admin field UI. The
 * Walker_Nav_Menu subclass `Brndle\Navigation\MegaMenuWalker` is
 * instantiated at the call site in header.blade.php, so it does not need
 * a service registration — just a stable namespace.
 *
 * @see plans/2026-05-03-mega-menu.md
 */

namespace Brndle\Providers;

use Brndle\Navigation\MegaSidebars;
use Brndle\Navigation\MenuItemMeta;

class NavigationServiceProvider
{
    /**
     * Bootstrap navigation hooks.
     *
     * @return void
     */
    public function boot(): void
    {
        MenuItemMeta::boot();
        MegaSidebars::boot();
    }
}
