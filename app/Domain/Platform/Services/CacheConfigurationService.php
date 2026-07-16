<?php
namespace App\Domain\Platform\Services;

use Illuminate\Support\Facades\Cache;

class CacheConfigurationService
{
    public function flushAllCaches(): void
    {
        Cache::flush();
    }
}
