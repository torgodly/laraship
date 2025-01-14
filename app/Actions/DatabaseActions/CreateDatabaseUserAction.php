<?php

namespace App\Actions\DatabaseActions;

use App\Exceptions\DatabaseActions\CreateDatabaseUserActionException;
use App\Services\ShellScriptService;
use Exception;

class CreateDatabaseUserAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Create a new database user.
     *
     * @param string $username The username of the new user.
     * @param string $password The password of the new user.
     * @return string Output of the shell script.
     * @throws CreateDatabaseUserActionException
     */
    public function execute(string $username, string $password): string
    {
        // Prepare the arguments for the shell script
        $arguments = [
            'username' => $username,
            'password' => $password
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/database/create_database_user.sh');

        // Call the ShellScriptService to run the script
        try {
            return $this->shellService->runScript($scriptPath, $arguments); // Return the output from the script
        } catch (Exception $e) {
            throw new CreateDatabaseUserActionException($e->getMessage());
        }
    }
}
