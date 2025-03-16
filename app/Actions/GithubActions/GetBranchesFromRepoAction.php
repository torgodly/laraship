<?php

namespace App\Actions\GithubActions;

use Illuminate\Support\Facades\Http;

class GetBranchesFromRepoAction
{
    public function execute($repository): array
    {
        // Extract user and repo from the GitHub URL
        preg_match('/https:\/\/github\.com\/([^\/]+)\/([^\/]+)/', $repository, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $user = $matches[1];
            $repo = $matches[2];

            // Fetch the branches using GitHub API
            $response = Http::get("https://api.github.com/repos/{$user}/{$repo}/branches");

            if ($response->successful()) {
                $branches = $response->json();
                // Return branches as options for the select input
                return collect($branches)->pluck('name', 'name')->toArray();
            }
        }

        return [];
    }
}
