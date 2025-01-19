<?php

namespace App\Actions\DatabaseActions;

use App\Models\DbUser;
use Filament\Facades\Filament;

class StoreDatabaseUserAction
{
    public function execute(string $username): DbUser
    {
        $tenantId = Filament::getTenant()->id;

        $dbUserEntry = DbUser::create([
            'name' => $username,
            'team_id' => $tenantId,
        ]);

        return $dbUserEntry;
    }
}
