<?php

use App\Actions\SourceActions\GetGitHubAppData;
use App\Actions\SourceActions\GetGithubAppRepositories;
use App\Filament\Clusters\Server\Resources\SourceResource\Pages\ViewSource;
use App\Models\Repository;
use App\Models\Source;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/github/{source:uuid}/create-app', function (Source $source) {
    $state = $source->uuid; // Generate a unique state value for security
    $manifest = [
        "name" => $source->app_name,
        "url" => "https://laraship.test",
        "hook_attributes" => [
            "url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/events",
            "active" => true,
        ],
        "redirect_url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/redirect",
        "callback_urls" => [
            "https://laraship.test/login/github/app",
        ],
        "public" => false,
        "request_oauth_on_install" => false,
        "setup_url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/install?source=$state",
        "setup_on_update" => true,
        "default_permissions" => [
            "contents" => "read",
            "metadata" => "read",
            "emails" => "read",
            "administration" => "read",
            "pull_requests" => "write",
        ],
        "default_events" => [
            "pull_request",
            "push",
        ],
    ];

    // Pass the manifest and state to the view
    return view('github.create-app', [
        'manifest' => json_encode($manifest),
        'state' => $state,
    ]);
})->name('github.create-app');


Route::get('/github/app/callback', function () {
    // Handle callback after installation or other GitHub API responses
    dd('GitHub App Callback');
});

Route::get('/login/github/app', function () {
    // Display confirmation or guide the user through any post-install setup steps
    dd('GitHub App Installed');
});


Route::post('/webhooks/{source:uuid}/github/events', function (Request $request) {
    // Handle incoming GitHub events
    Log::info('GitHub Event Received', $request->all());

    // Process the event (e.g., push, pull_request)
    // Example: log the event or trigger a custom action
});


Route::get('/webhooks/{source:uuid}/github/redirect', function (Request $request, Source $source) {
    Log::info('GitHub App Redirected', $request->all());
    return (new GetGitHubAppData($source, $request->code))->execute();
});

Route::get('/webhooks/{source:uuid}/github/install', function (Request $request, Source $source) {
    Log::info('GitHub App Installed', $request->all());
    $source->update(['installation_id' => $request->installation_id]);
    Notification::make()
        ->title('GitHub App Installed')
        ->body('You have successfully installed the GitHub App.')
        ->success()
        ->send();
    return redirect()->to(ViewSource::getUrl([$source], tenant: Auth::user()?->teams()->first()));
});

//test
Route::get('/test', function () {
    $source = Source::first();
    Repository::setSource($source);
    $repos = Repository::all();
    dd($repos);
});



