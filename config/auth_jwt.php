<?php

return [
    'issuer' => env('AUTH_JWT_ISSUER', 'http://localhost'),
    'jwks_url' => env('AUTH_JWKS_URL', 'http://localhost/api/.well-known/jwks.json'),
    'default_audiences' => array_filter(array_map('trim', explode(',', env('AUTH_JWT_DEFAULT_AUD', 'podcasts')))),
    'clock_skew' => (int) env('AUTH_JWT_SKEW', 60),
    'jwks_cache_ttl' => (int) env('AUTH_JWKS_CACHE_TTL', 900),
];
