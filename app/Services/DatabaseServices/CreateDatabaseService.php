<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\CreateDatabaseAction;
use App\Actions\DatabaseActions\CreateDatabaseUserAction;
use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Actions\DatabaseActions\StoreDatabaseAction;
use App\Actions\DatabaseActions\StoreDatabaseUserAction;
use App\Actions\DatabaseActions\SyncDatabaseUserAction;
use App\Models\Database;
use App\Services\ShellScriptService;
use Exception;
use Filament\Facades\Filament;

class CreateDatabaseService
{
    private CreateDatabaseAction $createDatabaseAction;
    private CreateDatabaseUserAction $createDatabaseUserAction;
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;
    private StoreDatabaseAction $storeDatabaseAction;
    private StoreDatabaseUserAction $storeDatabaseUserAction;
    private SyncDatabaseUserAction $syncDatabaseUserAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->createDatabaseAction = new CreateDatabaseAction();
        $this->createDatabaseUserAction = new CreateDatabaseUserAction();
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->storeDatabaseAction = new StoreDatabaseAction();
        $this->storeDatabaseUserAction = new StoreDatabaseUserAction();
        $this->syncDatabaseUserAction = new SyncDatabaseUserAction();
        $this->shellService = new ShellScriptService();

    }

    /**
     * @throws Exception
     */
    public function execute(string $database, ?string $username = null, ?string $password = null): string
    {
        try {
            // Step 1: Create the database
            $creationOutput = $this->createDatabaseAction->execute($database);

            // Step 2: Create the database user and link to the database (if username and password are provided)
            if ($username && $password) {
                $creationOutput .= $this->createDatabaseUserAction->execute($username, $password);
                $creationOutput .= $this->linkUserToDatabaseAction->execute($username, [$database]);
            }

            // Step 3: Execute the shell script for additional setup
            $scriptResults = $this->shellService->runScript($creationOutput);

            // Step 4: Store database and user in the system and link them
            $dbEntry = $this->storeDatabaseAction->execute($database);
            if ($username && $password) {
                $dbUserEntry = $this->storeDatabaseUserAction->execute($username);
                $this->syncDatabaseUserAction->execute($dbUserEntry, $dbEntry->id);
            }

            return $scriptResults;
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to create database and user: " . $e->getMessage());
        }
    }

}
