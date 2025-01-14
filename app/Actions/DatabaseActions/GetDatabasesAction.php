<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class GetDatabasesAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Get a list of available databases.
     *
     * @return array List of database names.
     */
    public function execute(): array
    {
        // Path to the shell script
        $scriptPath = base_path('app/Scripts/get_databases.sh');

        // Call the ShellScriptService to run the script
        $output = $this->shellService->runScript($scriptPath);

        // Convert the output into an array
        return explode("\n", trim($output));
    }
}
