<?php

namespace App\Actions\DatabaseActions;

use App\Exceptions\DatabaseActions\CreateDatabaseActionException;
use App\Services\ShellScriptService;
use Exception;

class CreateDatabaseAction
{
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Create a new database.
     *
     * @param string $database The name of the new database.
     * @return ?string Output of the shell script.
     * @throws CreateDatabaseActionException
     */
    public function execute(string $database): ?string
    {
        // Prepare the arguments for the shell script
        $arguments = [
            'database' => $database
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/database/create_database.sh');

        // Call the ShellScriptService to run the script
        try {
            return $this->shellService->runScript($scriptPath, $arguments); // Return the output from the script
        } catch (Exception $e) {
            throw new CreateDatabaseActionException($e->getMessage());
        }
    }

}
