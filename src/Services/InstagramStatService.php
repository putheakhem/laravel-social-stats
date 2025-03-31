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
     * @param  string|null  $handle  If provided, the method will try to fetch the count for the account matching this handle.
     */
    public function fetchCount(?string $handle = null): int
    {
        /** @var array<int> $cacheTtl */
        $cacheTtl = config('social-stats.instagram.ttl', 'social-stats.cache.default_ttl');
        $cacheKey = $this->getCacheKey($handle);

        $result = Cache::flexible($cacheKey, $cacheTtl, function () use ($handle): int {
            return $this->getFollowersCountFromApi($handle);
        });

        if (! is_int($result)) {
            Log::warning('[InstagramStatService] Cache returned non-int value for key '.$cacheKey.'. Returning 0.', []);

            return 0;
        }

        return $result;
    }

    /**
     * Generate a cache key based on the provided Instagram handle.
     */
    private function getCacheKey(?string $handle = null): string
    {
        return $handle
            ? 'instagram_followed_by_count_'.$handle
            : 'instagram_followed_by_count';
    }

    /**
     * Retrieve the follower count from the Instagram Graph API via the linked Facebook Page.
     *
     * @param  string|null  $handle  The Instagram handle to filter by (if provided).
     */
    private function getFollowersCountFromApi(?string $handle = null): int
    {
        $accessToken = config('social-stats.instagram.access_token');
        $pageId = config('social-stats.instagram.page_id');

        if (! $accessToken || ! $pageId) {
            Log::warning('[InstagramStatService] Missing Instagram access token or Facebook Page ID in config.', []);

            return 0;
        }

        // Cast pageId to string explicitly.
        $url = 'https://graph.facebook.com/v18.0/'.$pageId;
        $query = [
            // Include the 'username' field so we can match against $handle.
            'fields' => 'instagram_accounts{follow_count,followed_by_count,has_profile_picture,id,username}',
            'access_token' => $accessToken,
        ];

        if (config('app.debug')) {
            Log::info('[InstagramStatService] Fetching Instagram accounts linked to Facebook Page.', [
                'url' => $url,
                'query' => $query,
            ]);
        }

        try {
            $response = Http::withOptions(['verify' => false])->get($url, $query);

            if (! $response->successful()) {
                Log::warning('[InstagramStatService] Failed to fetch Instagram accounts.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return 0;
            }

            $data = $response->json();

            if (config('app.debug')) {
                Log::debug('[InstagramStatService] Facebook API response:', is_array($data) ? $data : ['data' => $data]);
            }

            // @phpstan-ignore-next-line
            $accounts = $data['instagram_accounts']['data'] ?? [];

            if (! is_array($accounts) || empty($accounts)) {
                Log::warning('[InstagramStatService] No Instagram accounts linked to this page.', []);

                return 0;
            }

            // If a handle is provided, look for a matching account.
            if ($handle !== null) {
                foreach ($accounts as $account) {
                    if (isset($account['username']) && strcasecmp($account['username'], $handle) === 0) {
                        $followerCount = $account['followed_by_count'] ?? null;
                        if (is_numeric($followerCount)) {
                            Log::info('[InstagramStatService] Follower count fetched successfully for handle '.$handle.'.', ['followers' => $followerCount]);

                            return (int) $followerCount;
                        }
                        Log::warning('[InstagramStatService] followed_by_count is missing or not numeric for account matching handle '.$handle.'.', ['account' => $account]);

                        return 0;
                    }
                }
                Log::warning('[InstagramStatService] No Instagram account found matching handle '.$handle.'.', ['accounts' => $accounts]);

                return 0;
            }
            // No handle provided; use the first account.
            $followerCount = $accounts[0]['followed_by_count'] ?? null;
            if (is_numeric($followerCount)) {
                Log::info('[InstagramStatService] Follower count fetched successfully from first account.', ['followers' => $followerCount]);

                return (int) $followerCount;
            }
            Log::warning('[InstagramStatService] followed_by_count is missing or not numeric in first account.', ['account' => $accounts[0] ?? []]);

        } catch (Throwable $e) {
            Log::error('[InstagramStatService] Exception during API call: '.$e->getMessage(), ['exception' => $e]);
        }

        return 0;
    }
}
