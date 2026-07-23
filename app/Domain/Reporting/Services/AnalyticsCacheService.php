<?php

namespace App\Domain\Reporting\Services;

use App\Models\AnalyticsCache;
use Carbon\Carbon;

class AnalyticsCacheService
{
    public function get(string $key)
    {
        $cached = AnalyticsCache::where('cache_key', $key)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($cached) {
            return json_decode($cached->cache_value, true);
        }

        return null;
    }

    public function put(string $key, $value, int $minutes = 60): void
    {
        AnalyticsCache::updateOrCreate(
            ['cache_key' => $key],
            [
                'cache_value' => json_encode($value),
                'expires_at' => Carbon::now()->addMinutes($minutes),
            ]
        );
    }

    public function clear(): void
    {
        AnalyticsCache::truncate();
    }
}
