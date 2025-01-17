<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\CreateDatabaseAction;
use App\Actions\DatabaseActions\CreateDatabaseUserAction;
use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Services\ShellScriptService;
use Exception;

class CreateDatabaseService
{
    private CreateDatabaseAction $createDatabaseAction;
    private CreateDatabaseUserAction $createDatabaseUserAction;
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->createDatabaseAction = new CreateDatabaseAction();
        $this->createDatabaseUserAction = new CreateDatabaseUserAction();
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->shellService = new ShellScriptService();

    }

    /**
     * @throws Exception
     */
    public function execute(string $database, ?string $username = 'laraship', ?string $password = null): string
    {
        try {
            $username = $username ?? 'laraship';
            // Step 1: Create the database
            $output = $this->createDatabaseAction->execute($database);

            // Step 2: Create the database user (if not the default user)
            if ($username !== 'laraship') {
                $output .= $this->createDatabaseUserAction->execute($username, $password);
            }
//
//            // Step 3: Link the user to the database
            $output .= $this->linkUserToDatabaseAction->execute($username, [$database]);
//            Log::info("Database and user created successfully: " . $output);
            return $this->shellService->runScript($output);
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to create database and user: " . $e->getMessage());
        }
    }

}
