<?php

namespace App\Actions\DatabaseActions;

use App\Exceptions\DatabaseActions\CreateDatabaseUserActionException;
use App\Services\ShellScriptService;

class CreateDatabaseUserAction
{
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
        return view('Scripts.DatabaseScripts.create_database_user', compact('username', 'password'));
    }
}
