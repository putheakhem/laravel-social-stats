<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats;

use InvalidArgumentException;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use PutheaKhem\LaravelSocialStats\Services\FacebookStatService;
use PutheaKhem\LaravelSocialStats\Services\InstagramStatService;
use PutheaKhem\LaravelSocialStats\Services\TelegramStatService;
use PutheaKhem\LaravelSocialStats\Services\YouTubeStatService;

final class SocialMediaStatManager
{
    public static function platform(string $platform): SocialMediaStatService
    {
        return match ($platform) {
            'telegram' => new TelegramStatService,
            'youtube' => new YouTubeStatService,
            'facebook' => new FacebookStatService,
            'instagram' => new InstagramStatService,
            default => throw new InvalidArgumentException("Unsupported platform: {$platform}"),
        };
    }
}
