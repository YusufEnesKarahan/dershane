<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;

class PingHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        return [
            'success' => true,
            'message' => 'pong',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
