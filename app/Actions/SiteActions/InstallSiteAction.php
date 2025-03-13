<?php

namespace App\Actions\SiteActions;

use App\Models\Site;

class InstallSiteAction
{
    public function execute(Site $site): string
    {
        // Path to the shell script
        return view('Scripts.SitesScripts.install-site', ['site' => $site])->render();
    }
}
