<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Actions\DatabaseActions\SyncDatabaseUserAction;
use App\Models\Database;
use App\Models\DbUser;
use Exception;

class LinkUserToDatabaseService
{
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;
    private SyncDatabaseUserAction $syncDatabaseUserAction;

    public function __construct()
    {
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->syncDatabaseUserAction = new SyncDatabaseUserAction();
    }

    /**
     * Links a user to multiple databases and synchronizes them.
     *
     * @param DbUser $db_user
     * @param array $databases
     * @return string
     * @throws Exception
     */
    public function execute(DbUser $db_user, array $databases): string
    {
        try {
            // Link the user to the specified databases
            $output = $this->linkUserToDatabaseAction->execute($db_user->username, $databases);

            // Fetch database IDs for synchronization
            $databaseIds = Database::whereIn('id', $databases)->pluck('id')->toArray();

            // Sync the user with the corresponding databases
            $this->syncDatabaseUserAction->execute($db_user, $databaseIds);

            return $output; // Return output from the link action
        } catch (Exception $e) {
            throw new \RuntimeException('Failed to link user to database: ' . $e->getMessage());
        }
    }
}
