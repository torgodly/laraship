<?php

namespace App\Actions\DatabaseActions;

use App\Exceptions\DatabaseActions\LinkUserToDatabaseActionException;
use App\Services\ShellScriptService;
use Exception;

class LinkUserToDatabaseAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Link a user to one or more databases.
     *
     * @param string $username The username to link to databases.
     * @param array $databases The list of databases to link to the user.
     * @return string Output of the shell script.
     * @throws LinkUserToDatabaseActionException
     */
    public function execute(string $username, array $databases): string
    {
        // Prepare the arguments for the shell script
        $arguments = [
            'username' => $username,
            'databases' => implode(',', $databases) // Convert array of databases to a comma-separated string
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/database/link_user_to_database.sh');

        // Call the ShellScriptService to run the script
        try {
            return $this->shellService->runScript($scriptPath, $arguments); // Return the output from the script
        } catch (Exception $e) {
            throw new LinkUserToDatabaseActionException($e->getMessage());
        }
    }
}
