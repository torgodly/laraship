<?php

namespace App\Actions\DatabaseActions;

use App\Models\Database;
use Illuminate\Support\Facades\DB;

class DeleteDatabaseAction
{
    /**
     * @throws \Exception
     */
    public function execute(Database $database): true
    {
        DB::beginTransaction();

        try {
            // Detach the database from all users
            $database->users()->detach();

            // Delete the database
            $database->delete();

            // Commit transaction
            DB::commit();

            // Return true to indicate success
            return true;
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }
}
