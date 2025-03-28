<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats;

use Illuminate\Support\ServiceProvider;

final class SocialStatsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/social-stats.php', 'social-stats');

        $this->app->singleton('social-stats', function () {
            return new SocialMediaStatManager;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/social-stats.php' => config_path('social-stats.php'),
        ], 'config');
    }
}
