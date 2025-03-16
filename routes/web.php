<?php

use App\Http\Controllers\GitHubAppController;
use App\Models\Site;
use App\Models\Source;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;



Route::get('/', function () {
    // Wrap the full command with double quotes to execute properly in bash
    $command = 'bash -c "echo [$(update-alternatives --display php | grep \'link currently points to\' | awk -F\'/\' \'{print \\\"php\\\"$NF}\' | sed \'s/^phpphp/php/\' | paste -sd,)]"';

    $process = Process::fromShellCommandline($command);
    $process->run();

    // Check for errors first
    if (!$process->isSuccessful()) {
        return response($process->getErrorOutput(), 500);
    }

    // Output the result
    return $process->getOutput();
});

Route::get('/github/{source:uuid}/create-app', [GitHubAppController::class, 'createApp'])->name('github.create-app');


Route::get('/github/{source:uuid}/callback', function (Source $source) {
    return response()->json(['message' => 'GitHub App Callback', $source->app_name]);
})->name('github.callback');

Route::post('/webhooks/{source:uuid}/github/events', [GitHubAppController::class, 'handleGitHubEvent'])->name('github.events');


Route::get('/webhooks/{source:uuid}/github/redirect', [GitHubAppController::class, 'redirect'])->name('github.redirect');

Route::get('/webhooks/{source:uuid}/github/install', [GitHubAppController::class, 'install'])->name('github.install');


//endpoint to call when initializing a site to change the status to initialized
Route::get('/sites/{site:uuid}/initialize', function (Site $site) {
    $site->update(['status' => \App\Enums\SiteStatus::Initialized->value]);
    return response()->json(['message' => 'Site Initialized'], 200);
})->name('sites.initialize');


