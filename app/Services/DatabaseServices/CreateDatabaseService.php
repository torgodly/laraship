<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\CreateDatabaseAction;
use App\Actions\DatabaseActions\CreateDatabaseUserAction;
use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Actions\DatabaseActions\RemoveDatabaseAction;
use App\Actions\DatabaseActions\RemoveDatabaseUserAction;
use App\Exceptions\DatabaseActions\CreateDatabaseActionException;
use App\Exceptions\DatabaseActions\CreateDatabaseUserActionException;
use App\Exceptions\DatabaseActions\LinkUserToDatabaseActionException;
use Exception;
use Log;

class CreateDatabaseService
{
    private CreateDatabaseAction $createDatabaseAction;
    private CreateDatabaseUserAction $createDatabaseUserAction;
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;
    private RemoveDatabaseAction $removeDatabaseAction;
    private RemoveDatabaseUserAction $removeDatabaseUserAction;

    public function __construct()
    {
        $this->createDatabaseAction = new CreateDatabaseAction();
        $this->createDatabaseUserAction = new CreateDatabaseUserAction();
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->removeDatabaseAction = new RemoveDatabaseAction();
        $this->removeDatabaseUserAction = new RemoveDatabaseUserAction();
    }

    /**
     * @throws Exception
     */
    public function execute(string $database, ?string $username = 'laraship', ?string $password = 'password'): string
    {
        try {
            // Step 1: Create the database
            $output = $this->createDatabaseAction->execute($database);

            // Step 2: Create the database user (if not the default user)
            if ($username !== 'laraship') {
                $output .= $this->createDatabaseUserAction->execute($username, $password);
            }

            // Step 3: Link the user to the database
            $output .= $this->linkUserToDatabaseAction->execute($username, [$database]);
            Log::info("Database and user created successfully: " . $output);

            return $output;
        } catch (Exception $e) {
            $this->rollback($database, $username, $e);
            throw new \RuntimeException("Failed to create database and user: " . $e->getMessage());
        }
    }

    private function rollback(string $database, string $username, Exception $exception): void
    {
        // Log or notify about the exception (you can replace this with your logging/notification system)
        $this->notify("Error: " . $exception->getMessage());

        try {
            // Rollback logic based on the stage of failure
            if ($exception instanceof CreateDatabaseUserActionException) {
                // If user creation failed, delete the database
                $this->removeDatabaseAction->execute($database);
            } elseif ($exception instanceof LinkUserToDatabaseActionException) {
                // If linking failed
                if ($username !== 'laraship') {
                    $this->removeDatabaseUserAction->execute($username);
                }
                $this->removeDatabaseAction->execute($database);
            } elseif ($exception instanceof CreateDatabaseActionException) {
                // Database creation failure does not trigger rollback
                $this->notify("No rollback required for database creation failure.");
            }
        } catch (Exception $rollbackException) {
            // Log or notify about rollback failure
            $this->notify("Rollback failed: " . $rollbackException->getMessage());
        }
    }

    private function notify(string $message): void
    {
        // Replace this with your notification logic
        // e.g., send an email, write to a log, etc.
        Log::error($message);
    }
}
