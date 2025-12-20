<?php

use function Cake\Core\env;

return [
    'OpenRouter' => [
        'api_key' => env('OPENROUTER_API_KEY', null),
        'api_endpoint' => env('OPENROUTER_API_ENDPOINT', 'https://openrouter.ai/api/v1/'),
        'api_timeout' => env('OPENROUTER_API_TIMEOUT', 20),
        'title' => env('OPENROUTER_API_TITLE', 'CakePHP OpenRouter'),
        'referer' => env('OPENROUTER_API_REFERER', ''),
    ],
];
