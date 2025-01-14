<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class RemoveDatabaseAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Remove a database.
     *
     * @param string $databaseName The name of the database to remove.
     * @return string Output of the shell script.
     */
    public function execute(string $databaseName): string
    {
        // Prepare arguments for the shell script
        $arguments = [
            'database' => $databaseName,
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/database/remove_database.sh');

        // Call the ShellScriptService to run the script
        return $this->shellService->runScript($scriptPath, $arguments);
    }
}
