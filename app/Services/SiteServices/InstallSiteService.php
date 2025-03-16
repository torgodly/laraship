<?php

namespace App\Services\SiteServices;

use App\Actions\SiteActions\InstallSiteAction;
use App\Enums\DeploymentStatus;
use App\Models\Site;
use App\Services\ShellScriptService;

class InstallSiteService
{
    private InstallSiteAction $installSiteAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->installSiteAction = new InstallSiteAction();
        $this->shellService = new ShellScriptService();
    }

    public function execute(Site $site): string
    {
        try {
            // Initialize the site through action
            $script = $this->installSiteAction->execute($site);
            $output = $this->shellService->runScript($script);
            $site->deployments->first()
                ->update([
                    'status' => DeploymentStatus::Deployed->value,
                    'output' => $output,
                ]);
            return true;
        } catch (\Exception $e) {
            // Catch any exception and provide context
            \Log::error("Failed to initialize site: " . $e->getMessage());
            throw new \RuntimeException("Failed to initialize site: " . $e->getMessage(), 0, $e);
        }

    }
}
