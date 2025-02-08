<?php

namespace App\Actions\SourceActions;

use App\Models\Repository;
use App\Models\Source;
use App\Services\Helpers\ApiClient;

class GetRepositoryBranches
{
    protected Repository $repository;
    protected Source $source;
    protected GenerateInstallationAccessTokenAction $generateInstallationAccessTokenAction;
    protected ApiClient $apiClint;


    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->source = $repository->source;
        $this->generateInstallationAccessTokenAction = new GenerateInstallationAccessTokenAction($this->source);
        $this->apiClint = new ApiClient();
    }

    //execute

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $token = $this->generateInstallationAccessTokenAction->execute();
        $branches = $this->apiClint
            ->get(endpoint: $this->repository->branches_url, token: $token, data: ['per_page' => 100]);

        $result = [];
        foreach ($branches as $branch) {
            $name = $branch['name'];
            $result[$name] = $name;
        }
        $result = array_reverse($result, true);
        return $result;
    }

}
