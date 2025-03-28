<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Contracts;

interface SocialMediaStatService
{
    public function fetchCount(string $handle): int;
}
