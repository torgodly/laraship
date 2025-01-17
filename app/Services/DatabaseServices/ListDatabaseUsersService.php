<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\ListDatabaseUsersAction;
use App\Services\ShellScriptService;
use Exception;

class ListDatabaseUsersService
{
    private ListDatabaseUsersAction $listDatabasesUsersAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->listDatabasesUsersAction = new ListDatabaseUsersAction();
        $this->shellService = new ShellScriptService();
    }

    public function execute(): array
    {
        try {
            $output = $this->listDatabasesUsersAction->execute();
            $users = $this->shellService->runScript($output);
            return explode("\n", trim($users));
        } catch (Exception $e) {
            return [];
        }

    }
}
