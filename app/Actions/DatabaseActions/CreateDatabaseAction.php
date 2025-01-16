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
        // Path to the shell script
        return view('Scripts.DatabaseScripts.create_database', ['database' => $database]);
    }

}
