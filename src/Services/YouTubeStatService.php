<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use Throwable;

final class YouTubeStatService implements SocialMediaStatService
{
    /**
     * Fetch the subscriber count for the given YouTube channel ID.
     */
    public function fetchCount(string $channelId): int
    {
        $cacheKey = $this->getCacheKey($channelId);

        /** @var int $count */
        $count = Cache::flexible($cacheKey, [18000, 3600], function () use ($channelId): int {
            return $this->getSubscriberCountFromApi($channelId);
        });
        return $count;
    }

    /**
     * Generate a cache key for the YouTube channel subscriber count.
     */
    private function getCacheKey(string $channelId): string
    {
        return "youtube_subscriber_count_{$channelId}";
    }

    /**
     * Retrieve the subscriber count from the YouTube API.
     *
     * return int
     */
    private function getSubscriberCountFromApi(string $channelId): int
    {
        $apiKey = config('social-stats.youtube.api_key');

        if (empty($apiKey)) {
            Log::error('[YouTubeStatService] API key is not configured.');

            return 0;
        }

        $url = 'https://www.googleapis.com/youtube/v3/channels';

        try {
            $response = Http::get($url, [
                'part' => 'statistics',
                'id' => $channelId,
                'fields' => 'items/statistics/subscriberCount',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $subscriberCount = $response->json('items.0.statistics.subscriberCount');
                if (is_numeric($subscriberCount)) {
                    return (int) $subscriberCount;
                }
                Log::error("[YouTubeStatService] Received invalid subscriber count for channel {$channelId}.");
            } else {
                Log::error("[YouTubeStatService] API request failed with status {$response->status()} for channel {$channelId}.");
            }
        } catch (Throwable $e) {
            Log::error("[YouTubeStatService] Exception occurred: {$e->getMessage()}", ['exception' => $e]);
        }

        return 0;
    }
}
