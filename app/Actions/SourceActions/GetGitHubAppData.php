<?php

namespace App\Actions\SourceActions;

use App\Filament\Clusters\Server\Resources\SourceResource\Pages\ViewSource;
use App\Models\Source;
use Filament\Notifications\Notification;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GetGitHubAppData
{
    protected $source;
    protected $code;

    public function __construct(Source $source, string $code)
    {
        $this->source = $source;
        $this->code = $code;
    }

    public function execute()
    {
        $url = "https://api.github.com/app-manifests/" . $this->code . "/conversions";
        try {
            $response = Http::timeout(120)
                ->withHeaders(['Accept' => 'application/vnd.github+json'])
                ->withBody('{}', 'application/json')
                ->post("https://api.github.com/app-manifests/{$this->code}/conversions");
            if ($response->successful()) {  // Ensure you're using Laravel's HTTP Client
                $data = $response->json();
                $this->source->update([
                    'app_name' => $data['name'],
                    'app_id' => $data['id'],
                    'app_url' => $data['html_url'],
                    'description' => $data['description'],
                    'client_id' => $data['client_id'],
                    'client_secret' => $data['client_secret'],
                    'webhook_secret' => $data['webhook_secret'],
                    'private_key' => $data['pem'],
                    'permissions' => $data['permissions'],
                    'events' => $data['events'],
                    'owner' => $data['owner'],
                ]);
                Notification::make()
                    ->title('GitHub App Registered')
                    ->body('Your GitHub App has been successfully registered.')
                    ->success()
                    ->send();
                return redirect()->to(ViewSource::getUrl([$this->source], tenant: Auth::user()?->teams()->first()));
            } else {
                throw new \Exception("API Error: {$response->status()} - {$response->body()}");
            }
        } catch (ConnectionException $e) {
            throw new \Exception("Connection Failed: {$e->getMessage()}");
        }
    }
}
