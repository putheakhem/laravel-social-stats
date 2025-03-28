<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Facades;

use Illuminate\Support\Facades\Facade;

final class SocialStats extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'social-stats';
    }
}
