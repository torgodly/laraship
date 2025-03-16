<?php

namespace App\Actions\SystemActions\SiteActions;

use App\Enums\DeploymentActionType;
use App\Enums\DeploymentStatus;
use App\Models\Site;

class CreateSiteAction
{
    //execute the action
    public function execute($data)
    {
        //create site
        $site = Site::create($data);

        //create deployment
        $site->deployments()->create([
            'repository_url' => $data['repository'],
            'branch' => $data['branch'],
            'action_type' => DeploymentActionType::Manual->value,
            'status' => DeploymentStatus::Pending->value,
            'triggered_by' => auth()->user()->id,
        ]);

        return $site;
    }
}
