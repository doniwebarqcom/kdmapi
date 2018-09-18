<?php

return [
     /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => true,
    'allowedOrigins'      => [env('APP_ALLOWEDORIGINS', '*')],
    'allowedHeaders'      => ['Origin', 'Content-Type', 'Accept', 'Authorization', 'X-Request-With', 'Cache-Control', 'access-control-allow-origin', 'access-control-allow-method'],
    'allowedMethods'      => ['POST', 'GET', 'DELETE', 'PUT', 'OPTIONS'],
    'exposedHeaders'      => [],
    'maxAge'              => 0,
    'hosts'               => [],
];