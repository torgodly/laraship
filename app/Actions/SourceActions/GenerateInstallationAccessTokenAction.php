<?php

namespace App\Actions\SourceActions;

use App\Services\Helpers\ApiClient;
use Illuminate\Support\Str;

class GenerateInstallationAccessTokenAction
{
    protected $source;
    protected ApiClient $apiClint;
    protected GenerateGithubAppJWTAction $generateGithubAppJWTAction;

    public function __construct($source)
    {
        $this->source = $source;
        $this->apiClint = new ApiClient();
        $this->generateGithubAppJWTAction = new GenerateGithubAppJWTAction($source);
    }

    public function execute()
    {
        $jwt = $this->generateGithubAppJWTAction->execute();
        $endpoint = Str::replace(':installation_id', $this->source->installation_id, 'app/installations/:installation_id/access_tokens');
        $token = $this->apiClint
            ->post(endpoint: $endpoint, token: $jwt);
        $token = $token['token'];
        return $token;
    }
}
