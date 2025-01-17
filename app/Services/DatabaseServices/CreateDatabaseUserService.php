<?php

namespace App\Services\DatabaseServices;

use App\Actions\DatabaseActions\CreateDatabaseUserAction;
use App\Actions\DatabaseActions\LinkUserToDatabaseAction;
use App\Services\ShellScriptService;
use Exception;

class CreateDatabaseUserService
{
    private CreateDatabaseUserAction $createDatabaseUserAction;
    private LinkUserToDatabaseAction $linkUserToDatabaseAction;
    private ShellScriptService $shellService;

    public function __construct()
    {
        $this->createDatabaseUserAction = new CreateDatabaseUserAction();
        $this->linkUserToDatabaseAction = new LinkUserToDatabaseAction();
        $this->shellService = new ShellScriptService();

    }

    /**
     * @throws Exception
     */
    public function execute(string $username, string $password, array $databases): string
    {
        try {
            $output = $this->createDatabaseUserAction->execute($username, $password);
            if (!empty($databases)) {
                $output .= $this->linkUserToDatabaseAction->execute($username, $databases);
            }
            return $this->shellService->runScript($output);
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to create database user: " . $e->getMessage());
        }
    }
}
