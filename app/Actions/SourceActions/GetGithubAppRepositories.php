<?php

namespace App\Actions\SourceActions;

use App\Models\Source;
use App\Services\Helpers\ApiClient;

class GetGithubAppRepositories
{
    protected Source $source;
    protected $apiClint;
    protected GenerateInstallationAccessTokenAction $generateInstallationAccessTokenAction;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->apiClint = new ApiClient();
        $this->generateInstallationAccessTokenAction = new GenerateInstallationAccessTokenAction($source);
    }

    //execute

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $token = $this->generateInstallationAccessTokenAction->execute();
        $repositories = $this->apiClint
            ->get(endpoint: config('github-api.endpoints.repositories'), token: $token, data: ['per_page' => 100]);
        return $repositories;

    }


}
