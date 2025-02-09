<?php

use App\Http\Controllers\GitHubAppController;
use App\Models\Repository;
use App\Models\Source;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/github/{source:uuid}/create-app', [GitHubAppController::class, 'createApp'])->name('github.create-app');


Route::get('/github/app/callback', function () {
    dd('GitHub App Callback');
});

Route::post('/webhooks/{source:uuid}/github/events', [GitHubAppController::class, 'handleGitHubEvent']);


Route::get('/webhooks/{source:uuid}/github/redirect', [GitHubAppController::class, 'redirect']);

Route::get('/webhooks/{source:uuid}/github/install', [GitHubAppController::class, 'install']);

