<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class RemoveDatabaseAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Remove a database.
     *
     * @param string $databaseName The name of the database to remove.
     * @return \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function execute(string $databaseName): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return view('Scripts.DatabaseScripts.remove_database', ['database_name' => $databaseName]);
    }
}
