<?php

namespace App\Actions\DatabaseActions;

use App\Models\Database;
use App\Models\DbUser;
use App\Models\User;

class SyncDatabaseUserAction
{
    /**
     * Sync a user with one or multiple databases.
     *
     * @param DbUser $db_user
     * @param array|int $databaseIds
     * @return void
     */
    public function execute(DbUser $db_user, array|int $databaseIds): void
    {
        try {
            // Ensure $databaseIds is an array
            $databaseIds = is_array($databaseIds) ? $databaseIds : [$databaseIds];

            // Sync the user with the provided databases
            $db_user->databases()->sync($databaseIds);

        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to sync database(s) and user: " . $e->getMessage());
        }
    }
}
