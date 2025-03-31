<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PutheaKhem\LaravelSocialStats\Facades\SocialStats;

defined('LARAVEL_START') || define('LARAVEL_START', microtime(true));

uses(Tests\TestCase::class)
    ->beforeEach(function () {
        Config::set('social-stats.telegram.bot_token', 'fake-telegram-token');
        Config::set('social-stats.youtube.api_key', 'fake-youtube-key');
        Config::set('social-stats.facebook.access_token', 'fake-facebook-token');
    })
    ->in(__DIR__);

test('it fetches telegram followers', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => 1234], 200),
    ]);

    $count = SocialStats::platform('telegram')->fetchCount('some_channel');

    expect($count)->toBe(1234);
});

test('it fetches youtube subscribers', function () {
    Http::fake([
        'https://www.googleapis.com/youtube/v3/channels*' => Http::response([
            'items' => [['statistics' => ['subscriberCount' => '5678']]],
        ], 200),
    ]);

    $count = SocialStats::platform('youtube')->fetchCount('some_channel_id');

    expect($count)->toBe(5678);
});

test('it fetches facebook page likes', function () {
    Http::fake([
        'https://graph.facebook.com/*' => Http::response(['fan_count' => 4321], 200),
    ]);

    $count = SocialStats::platform('facebook')->fetchCount('some_page_id');

    expect($count)->toBe(4321);
});
