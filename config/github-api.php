<?php

return [
    'base_url' => env('GITHUB_API_BASE_URL', 'https://api.github.com/'),
    'timeout' => env('GITHUB_API_TIMEOUT', 10),
    'endpoints' => [
        'access_tokens' => 'app/installations/:installation_id/access_tokens',
        'repositories' => 'installation/repositories',
    ],
    'headers'=> [
        'Accept' => 'application/vnd.github.v3+json',
        'Authorization' => 'Bearer ',
    ],
];
