<?php

namespace App\Actions\SourceActions;

use App\Models\Source;
use App\Services\Helpers\ApiClient;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;

class GetGithubAppRepositories
{
    protected Source $source;
    protected $apiClint;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->apiClint = new ApiClient();
    }

    //execute

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $appId = $this->source->app_id;
        $privateKey = $this->source->private_key;

        $payload = [
            'iat' => time(),
            'exp' => time() + (10 * 60), // JWT expiration time (10 minutes)
            'iss' => $appId,
        ];

        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        $endpoint = Str::replace(':installation_id', $this->source->installation_id, 'app/installations/:installation_id/access_tokens');
        $token = $this->apiClint
            ->post(endpoint: $endpoint, token: $jwt);
        $token = $token['token'];

        $repositories = $this->apiClint
            ->get(endpoint: config('github-api.endpoints.repositories'), token: $token, data: ['per_page' => 100]);

        return $repositories;

    }


}
