<?php

namespace App\Actions\SourceActions;

use App\Models\Source;
use Firebase\JWT\JWT;

class GenerateGithubAppJWTAction
{
    protected Source $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    //execute
    public function execute(?int $expirationTime = 10)
    {
        $appId = $this->source->app_id;
        $privateKey = $this->source->private_key;

        $payload = [
            'iat' => time(),
            'exp' => time() + ($expirationTime * 60), // JWT expiration time (10 minutes)
            'iss' => $appId,
        ];

        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
    }
}
