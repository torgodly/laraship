<?php

namespace App\Enums;

enum UbuntuVersionsEnum: string
{
    case VERSION_24_04 = '24.04';
    case VERSION_22_04 = '22.04';
    case VERSION_20_04 = '20.04';

    public function label(): string
    {
        return match($this) {
            self::VERSION_24_04 => 'Ubuntu 24.04',
            self::VERSION_22_04 => 'Ubuntu 22.04',
            self::VERSION_20_04 => 'Ubuntu 20.04',
        };
    }
}
