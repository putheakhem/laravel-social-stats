<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use Throwable;

final class FacebookStatService implements SocialMediaStatService
{
    /**
     * Fetch the fan count for the given Facebook page ID.
     */
    public function fetchCount(string $pageId): int
    {
        /** @var array<int> $cacheTtl */
        $cacheTtl = config('social-stats.facebook.ttl', 'social-stats.cache.default_ttl');
        $cacheKey = $this->getCacheKey($pageId);

        /** @var int $count */
        $count = Cache::flexible($cacheKey, $cacheTtl, function () use ($pageId): int {
            return $this->getFanCountFromApi($pageId);
        });

        return $count;
    }

    /**
     * Generate a cache key for the Facebook page fan count.
     */
    private function getCacheKey(string $pageId): string
    {
        return "facebook_fan_count_{$pageId}";
    }

    /**
     * Retrieve the fan count from the Facebook Graph API.
     */
    private function getFanCountFromApi(string $pageId): int
    {
        $accessToken = config('social-stats.facebook.access_token');

        if (empty($accessToken)) {
            Log::error('[FacebookStatService] Access token is not configured.');

            return 0;
        }

        $url = "https://graph.facebook.com/v12.0/{$pageId}";

        try {
            $response = Http::get($url, [
                'fields' => 'fan_count',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $fanCount = $response->json('fan_count');
                if (is_numeric($fanCount)) {
                    return (int) $fanCount;
                }
                Log::error("[FacebookStatService] Received invalid fan count for page {$pageId}.");
            } else {
                Log::error("[FacebookStatService] API request failed with status {$response->status()} for page {$pageId}.");
            }
        } catch (Throwable $e) {
            Log::error("[FacebookStatService] Exception occurred: {$e->getMessage()}", ['exception' => $e]);
        }

        return 0;
    }
}
