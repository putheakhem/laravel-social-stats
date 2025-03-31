<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use Throwable;

final class InstagramStatService implements SocialMediaStatService
{
    /**
     * Fetch the Instagram follower count.
     *
     * @param  string|null  $handle  Optionally, the Instagram handle; if provided, it will be used in the cache key.
     */
    public function fetchCount(?string $handle = null): int
    {
        $cacheKey = $this->getCacheKey($handle);
        $cacheTtl = [18000, 3600];

        $result = Cache::flexible($cacheKey, $cacheTtl, function (): int {
            return $this->getFollowersCountFromApi();
        });

        if (! is_int($result)) {
            Log::warning("[InstagramStatService] Cache returned non-int value for key {$cacheKey}. Returning 0.", []);

            return 0;
        }

        return $result;
    }

    /**
     * Generate a cache key for the Instagram follower count.
     */
    private function getCacheKey(?string $handle = null): string
    {
        return $handle
            ? "instagram_followed_by_count_{$handle}"
            : 'instagram_followed_by_count';
    }

    /**
     * Retrieve the follower count from the Instagram Graph API.
     */
    private function getFollowersCountFromApi(): int
    {
        $accessToken = config('social-stats.instagram.access_token');
        $instagramId = config('social-stats.instagram.business_id');

        if (! $accessToken || ! $instagramId) {
            Log::warning('[InstagramStatService] Missing Instagram access token or business ID in config.', []);

            return 0;
        }

        // Cast $instagramId to string explicitly to avoid type issues.
        $url = 'https://graph.facebook.com/v12.0/'.$instagramId;
        $query = [
            'fields' => 'followers_count',
            'access_token' => $accessToken,
        ];

        if (config('app.debug')) {
            Log::info('[InstagramStatService] Fetching followers count using business ID.', ['url' => $url, 'query' => $query]);
        }

        try {
            $response = Http::withOptions(['verify' => false])->get($url, $query);

            if ($response->successful()) {
                $data = $response->json();

                if (config('app.debug')) {
                    Log::debug('[InstagramStatService] API response received:', is_array($data) ? $data : ['data' => $data]);
                }

                if (is_array($data) && isset($data['followers_count']) && is_numeric($data['followers_count'])) {
                    $followers = (int) $data['followers_count'];

                    if (config('app.debug')) {
                        Log::info('[InstagramStatService] Followers count retrieved successfully.', ['followers' => $followers]);
                    }

                    return $followers;
                }

                Log::warning('[InstagramStatService] followers_count not found or invalid in API response.', ['data' => $data]);
            } else {
                Log::warning('[InstagramStatService] Failed to fetch followers count from API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('[InstagramStatService] Exception thrown while fetching followers count: '.$e->getMessage(), ['exception' => $e]);
        }

        return 0;
    }
}
