<?php

namespace App\Services\Helpers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('github-api.base_url');
    }

    /**
     * Send a POST request.
     */
    public function post(string $endpoint, string $token, ?array $data = [])
    {
        try {
            $response = Http::timeout(config('github-api.timeout'))
                ->baseUrl($this->baseUrl)
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3+json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post($endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception("API Error: {$response->status()} - {$response->body()}");
        } catch (ConnectionException $e) {
            throw new \Exception("Connection Failed: {$e->getMessage()}");
        }
    }

    // Add more methods for GET,
    public function get(string $endpoint, string $token, ?array $data = [])
    {
        try {
            $response = Http::timeout(config('github-api.timeout'))
                ->withHeaders([
                    'Accept' => 'application/vnd.github.v3+json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->baseUrl($this->baseUrl)
                ->get($endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception("API Error: {$response->status()} - {$response->body()} - {$response->effectiveUri()}");
        } catch (ConnectionException $e) {
            throw new \Exception("Connection Failed: {$e->getMessage()}");
        }
    }
}
