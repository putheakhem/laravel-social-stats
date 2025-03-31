<?php

declare(strict_types=1);

return [
    'cache' => [

        'default_ttl' => [18000, 3600], // Default cache TTL in seconds (5 hours, 1 hour)
    ],
    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
        'ttl' => [18000, 3600],
    ],
    'facebook' => [
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        'ttl' => [18000, 3600],
    ],
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'ttl' => [18000, 3600],
    ],
    'instagram' => [
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        'page_id' => env('INSTAGRAM_PAGE_ID'),
        'ttl' => [18000, 3600],
    ],
];
