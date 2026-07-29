<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;
use Illuminate\Support\Facades\Cache;

class ClearCacheHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        // Safe clear cache without artisan call
        Cache::flush();
        
        return [
            'success' => true,
            'message' => 'Cache flushed successfully.',
        ];
    }
}
