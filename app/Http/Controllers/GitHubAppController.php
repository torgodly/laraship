<?php

namespace App\Http\Controllers;

use App\Actions\SourceActions\GetGitHubAppData;
use App\Filament\Clusters\Server\Resources\SourceResource\Pages\ViewSource;
use App\Models\Source;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GitHubAppController extends Controller
{
    //createApp
    public function createApp(Source $source)
    {
        $state = $source->uuid; // Generate a unique state value for security
        $manifest = [
            "name" => $source->app_name,
            "url" => "https://laraship.test",
            'description' => $source->description ?? 'A GitHub App for Laraship',
            "hook_attributes" => [
                "url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/events",
                "active" => true,
            ],
            "redirect_url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/redirect",
            "callback_urls" => [
                "https://laraship.test/github/" . $source->uuid . "/callback",
            ],
            "public" => false,
            "request_oauth_on_install" => false,
            "setup_url" => "https://laraship.test/webhooks/" . $source->uuid . "/github/install",
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
            'state' => $state,
            'manifest' => $manifest,
            'url' => $source->organization_name
                ? "https://github.com/organizations/{$source->organization_name}/settings/apps/new"
                : "https://github.com/settings/apps/new",
        ]);
    }

    //handleGitHubEvent
    public function handleGitHubEvent(Request $request, Source $source)
    {
        // Handle incoming GitHub events
        Log::info('GitHub Event Received', $request->all());

        // Process the event (e.g., push, pull_request)
        // Example: log the event or trigger a custom action
    }

    //redirect
    public function redirect(Request $request, Source $source)
    {
        Log::info('GitHub App Redirected', $request->all());
        return (new GetGitHubAppData($source, $request->code))->execute();
    }

    //installApp
    public function install(Request $request, Source $source)
    {
        Log::info('GitHub App Installed', $request->all());
        $source->update(['installation_id' => $request->installation_id]);
        Notification::make()
            ->title('GitHub App Installed')
            ->body('You have successfully installed the GitHub App.')
            ->success()
            ->send();
        return redirect()->to(ViewSource::getUrl([$source], tenant: Auth::user()?->teams()->first()));
    }
}
