<?php

namespace App\Enums;

use App\Traits\Enum;

enum ServerStatusEnum: string
{
    use Enum;

    case Connected = 'connected';
    case Building = 'building';
    case Starting = 'starting';
    case Stopped = 'stopped';
    case Updating = 'updating';
    case Maintenance = 'maintenance';

    case Error = 'error';


}
