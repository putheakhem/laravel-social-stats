<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use Throwable;

final class TikTokStatService implements SocialMediaStatService
{
    public function fetchCount(?string $username = null): int
    {
        $configUsername = config('social-stats.tiktok.username');

        if ($username === null && is_string($configUsername)) {
            $username = $configUsername;
        }

        if (! is_string($username) || trim($username) === '') {
            Log::warning('[TikTokStatService] Missing or invalid TikTok username.');

            return 0;
        }
        /** @var array<int> $cacheTtl */
        $cacheTtl = config('social-stats.tiktok.ttl', 'social-stats.cache.default_ttl');
        $cacheKey = "tiktok_follower_count_{$username}";

        /** @var int $count */
        $count = Cache::flexible($cacheKey, $cacheTtl, function () use ($username): int {
            return $this->getFollowerCountFromProfile($username);
        });

        return $count;
    }

    private function getFollowerCountFromProfile(string $username): int
    {
        $url = "https://www.tiktok.com/@{$username}";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'text/html',
            ])->withOptions(['verify' => false])->get($url);

            if ($response->ok()) {
                $html = $response->body();
                $count = $this->extractFollowerCount($html);

                if (is_int($count)) {
                    return $count;
                }

                Log::warning('[TikTokStatService] Follower count not found in TikTok HTML.', [
                    'html_snippet' => mb_substr($html, 0, 300),
                ]);
            } else {
                Log::warning('[TikTokStatService] TikTok profile request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('[TikTokStatService] Exception while fetching TikTok profile.', [
                'message' => $e->getMessage(),
            ]);
        }

        return 0;
    }

    private function extractFollowerCount(string $html): ?int
    {
        if (preg_match('/"followerCount":(\d+)/', $html, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
