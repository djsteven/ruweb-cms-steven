<?php

return [

    'clients' => [
        env('OAUTH_CLIENT_ID', 'claude-ai') => [
            'secret'        => env('OAUTH_CLIENT_SECRET', ''),
            'redirect_uris' => array_filter(explode(',', env('OAUTH_ALLOWED_REDIRECT_URIS', ''))),
        ],
    ],

    'auth_code_ttl'    => 300,        // 5 minutes
    'access_token_ttl' => 3600 * 8,  // 8 hours

];
