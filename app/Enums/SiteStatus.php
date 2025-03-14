<?php

namespace App\Enums;

enum SiteStatus: string
{
    case Pending = 'Pending';
    case Initialized = 'Initialized';

    case Deployed = 'Deployed';
}
