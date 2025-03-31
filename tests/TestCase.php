<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use PutheaKhem\LaravelSocialStats\Facades\SocialStats;
use PutheaKhem\LaravelSocialStats\SocialStatsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SocialStatsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'SocialStats' => SocialStats::class,
        ];
    }
}
