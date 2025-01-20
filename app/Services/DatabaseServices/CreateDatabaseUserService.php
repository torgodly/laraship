<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\CreateDatabaseUserAction;
use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Actions\DatabaseActions\StoreDatabaseUserAction;
use App\Actions\DatabaseActions\SyncDatabaseUserAction;
use App\Models\Database;
use App\Services\ShellScriptService;
use Exception;

class CreateDatabaseUserService
{
    private CreateDatabaseUserAction $createDatabaseUserAction;
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;

    private StoreDatabaseUserAction $storeDatabaseUserAction;
    private SyncDatabaseUserAction $syncDatabaseUserAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->createDatabaseUserAction = new CreateDatabaseUserAction();
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->shellService = new ShellScriptService();

    }

    /**
     * @throws Exception
     */
    public function execute(string $username, string $password, array $databases): string
    {
        try {
            // Step 1: Create the database user
            $output = $this->createDatabaseUserAction->execute($username, $password);

            // Step 2: Link the user to the databases if provided
            if (!empty($databases)) {
                $output .= $this->linkUserToDatabaseAction->execute($username, $databases);
            }

            // Step 3: Execute any shell script necessary for the process
            $scriptResult = $this->shellService->runScript($output);

            // Step 4: Store the database user
            $dbUserEntry = $this->storeDatabaseUserAction->execute($username);

            // Step 5: Sync the user to the databases
            if (!empty($databases)) {
                $databasesIds = Database::whereIn('name', $databases)->pluck('id')->toArray();
                $this->syncDatabaseUserAction->execute($dbUserEntry, $databasesIds);
            }

            return $scriptResult; // Return the result from the shell script execution
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to create database user: " . $e->getMessage());
        }
    }
}
