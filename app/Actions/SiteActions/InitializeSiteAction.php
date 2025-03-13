<?php

namespace App\Actions\SiteActions;

use App\Models\Site;

class InitializeSiteAction
{
    public function execute(Site $site): string
    {
        // Path to the shell script
        return view('Scripts.SitesScripts.initialize-site', ['site' => $site])->render();
    }
}
