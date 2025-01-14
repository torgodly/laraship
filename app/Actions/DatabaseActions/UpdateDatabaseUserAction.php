<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class UpdateDatabaseUserAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Update the username, password, and databases the user has privileges on.
     *
     * @param string $oldUsername The current username of the user.
     * @param string $newUsername The new username for the user.
     * @param string $newPassword The new password for the user.
     * @param array $databases The list of databases the user should have privileges on.
     * @return string Output of the shell script.
     */
    public function execute(string $oldUsername, string $newUsername, string $newPassword, array $databases): string
    {
        // Prepare the arguments for the shell script
        $arguments = [
            'old_username' => $oldUsername,
            'new_username' => $newUsername,
            'new_password' => $newPassword,
            'databases' => implode(',', $databases) // Convert array of databases to a comma-separated string
        ];

        // Path to the shell script
        $scriptPath = base_path('app/Scripts/update_database_user.sh');

        // Call the ShellScriptService to run the script
        return $this->shellService->runScript($scriptPath, $arguments);
    }
}
