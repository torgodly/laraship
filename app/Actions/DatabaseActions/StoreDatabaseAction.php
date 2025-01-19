<?php

namespace App\Actions\DatabaseActions;

use App\Models\Database;
use Filament\Facades\Filament;

class StoreDatabaseAction
{
    public function execute(string $database): Database
    {
        $tenantId = Filament::getTenant()->id;

        $dbEntry = Database::create([
            'name' => $database,
            'team_id' => $tenantId,
        ]);

        return $dbEntry;
    }
}
