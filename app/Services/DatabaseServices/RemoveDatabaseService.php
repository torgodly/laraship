<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\DeleteDatabaseAction;
use App\Actions\DatabaseActions\RemoveDatabaseAction;
use App\Models\Database;
use App\Services\ShellScriptService;
use RuntimeException;

class RemoveDatabaseService
{

    private RemoveDatabaseAction $removeDatabaseAction;
    private ShellScriptService $shellService;
    private DeleteDatabaseAction $deleteDatabaseAction;

    public function __construct()
    {
        $this->removeDatabaseAction = new RemoveDatabaseAction();
        $this->shellService = new ShellScriptService();
        $this->deleteDatabaseAction = new DeleteDatabaseAction();
    }

    public function execute(Database $database): string
    {
        try {
            // Remove the database through action
            $output = $this->removeDatabaseAction->execute($database->name);

            // Run the shell script
            $scriptResults = $this->shellService->runScript($output);

            // Delete the database
            $this->deleteDatabaseAction->execute($database);

            return $scriptResults;
        } catch (\Exception $e) {
            // Catch any exception and provide context
            throw new RuntimeException("Failed to remove database: " . $e->getMessage(), 0, $e);
        }
    }
}
