<?php

namespace App\Services\SiteServices;

use App\Actions\SiteActions\InitializeSiteAction;
use App\Models\Site;
use App\Services\ShellScriptService;

class InitializeSiteService
{
    private InitializeSiteAction $initializeSiteAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->initializeSiteAction = new InitializeSiteAction();
        $this->shellService = new ShellScriptService();
    }

    public function execute(Site $site): string
    {
        try {
            // Initialize the site through action
            $script = $this->initializeSiteAction->execute($site);
            $output = $this->shellService->runScript($script);
            //set the site as initialized
            $site->update(['initialized' => true]);
            return true;
        } catch (\Exception $e) {
            // Catch any exception and provide context
            throw new \RuntimeException("Failed to initialize site: " . $e->getMessage(), 0, $e);
        }

    }
}
