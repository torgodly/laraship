<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\ListDatabasesAction;
use App\Services\ShellScriptService;
use Exception;

class ListDatabasesService
{
    private ListDatabasesAction $listDatabasesAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->listDatabasesAction = new ListDatabasesAction();
        $this->shellService = new ShellScriptService();
    }

    public function execute(): array
    {
        try {
            $output = $this->listDatabasesAction->execute();
            $databases = $this->shellService->runScript($output);
            return explode("\n", trim($databases));
        } catch (Exception $e) {
            return [];
        }

    }
}
