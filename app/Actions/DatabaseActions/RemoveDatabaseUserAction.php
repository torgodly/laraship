<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class RemoveDatabaseUserAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Remove a database user.
     *
     * @param string $username The username of the user to remove.
     * @return string Output of the shell script.
     */
    public function execute(string $username): string
    {
        // Prepare arguments for the shell script
        $arguments = [
            'username' => $username,
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/database/remove_database_user.sh');

        // Call the ShellScriptService to run the script
        return $this->shellService->runScript($scriptPath, $arguments);
    }
}
