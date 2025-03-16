<?php

namespace App\Enums;

enum DeploymentActionType: string
{

    //somebody is pushing code to the repository
    case Push = 'Push';

    //somebody manually triggered the deployment
    case Manual = 'Manual';
}
