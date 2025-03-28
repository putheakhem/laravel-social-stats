<?php

declare(strict_types=1);

return [
    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
    ],
    'facebook' => [
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    ],
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    ],
];
