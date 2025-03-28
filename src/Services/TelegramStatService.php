<?php

declare(strict_types=1);

namespace PutheaKhem\LaravelSocialStats\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PutheaKhem\LaravelSocialStats\Contracts\SocialMediaStatService;
use Throwable;

final class TelegramStatService implements SocialMediaStatService
{
    /**
     * Fetch the follower count for the given Telegram handle.
     */
    public function fetchCount(string $handle): int
    {
        $cacheKey = $this->getCacheKey($handle);

        /** @var int $count */
        $count = Cache::flexible($cacheKey, [18000, 3600], function () use ($handle): int {
            return $this->getFollowerCountFromApi($handle);
        });

        return $count;
    }

    /**
     * Generate a cache key for the Telegram follower count.
     */
    private function getCacheKey(string $handle): string
    {
        return "telegram_follower_count_{$handle}";
    }

    /**
     * Retrieve the follower count from the Telegram API.
     */
    private function getFollowerCountFromApi(string $handle): int
    {
        $botToken = config('social-stats.telegram.bot_token');

        if (empty($botToken)) {
            Log::error('[TelegramStatService] Bot token is not configured.');

            return 0;
        }

        $apiEndpoint = 'https://api.telegram.org/bot'.$botToken.'/getChatMembersCount';

        try {
            $response = Http::get($apiEndpoint, [
                'chat_id' => "@{$handle}",
            ]);

            if ($response->successful() && $response->json('ok')) {
                $result = $response->json('result');
                if (is_numeric($result)) {
                    return (int) $result;
                }
                Log::error("[TelegramStatService] Received invalid follower count for handle {$handle}.");
            } else {
                Log::error("[TelegramStatService] API request failed with status {$response->status()} for handle {$handle}. Response: ".$response->body());
            }
        } catch (Throwable $e) {
            Log::error("[TelegramStatService] Exception occurred: {$e->getMessage()}", ['exception' => $e]);
        }

        return 0;
    }
}
