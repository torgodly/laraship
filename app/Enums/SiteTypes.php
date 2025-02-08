<?php

namespace App\Enums;

use App\Traits\Enum;

enum SiteTypes: string
{
    use Enum;
    case php = 'PHP / Laravel / Symfony';
    case static = 'Static HTML / Nuxt.js / Next.js';

    public function label(): string
    {
        return match($this) {
            self::php => 'PHP / Laravel / Symfony',
            self::static => 'Static HTML / Nuxt.js / Next.js',
        };
    }
}
