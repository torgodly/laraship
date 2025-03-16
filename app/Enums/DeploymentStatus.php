<?php

namespace App\Enums;

enum DeploymentStatus: string
{
    case Pending = 'Pending';
    case Deploying = 'Deploying';
    case Deployed = 'Deployed';

    case Failed = 'Failed';

}
