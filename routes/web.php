<?php

use App\Http\Controllers\GitHubAppController;
use App\Models\Site;
use App\Models\Source;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;


Route::get('/', function () {
    $command = "update-alternatives --display php | grep 'link currently points to' | awk -F'/' '{print $NF}' | sed 's/^php/php/' | tr -d '\\n'";

    $process = Process::fromShellCommandline($command);
    $process->run();

    // Check for errors
    if (!$process->isSuccessful()) {
        return response($process->getErrorOutput(), 500);
    }

    // Clean up the output
    $output = trim($process->getOutput());
    return $output ? "[{$output}]" : '[]';
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


