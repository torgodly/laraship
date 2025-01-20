<?php

namespace App\Actions\DatabaseActions;

use App\Services\ShellScriptService;

class ListDatabasesAction
{

    public function execute(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
    {
        // Path to the shell script
        return view('Scripts.DatabaseScripts.list_databases');
    }
}
