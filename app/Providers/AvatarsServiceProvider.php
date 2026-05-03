<?php

namespace Brndle\Providers;

use Brndle\Avatars\LocalAvatar;

class AvatarsServiceProvider
{
    public function boot(): void
    {
        LocalAvatar::boot();
    }
}
