<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class ListDatabasesAction
{
    protected $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    /**
     * Get a list of available databases.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View List of database names.
     */
    public function execute(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
    {
        // Path to the shell script
        return view('Scripts.DatabaseScripts.list_databases');
    }
}