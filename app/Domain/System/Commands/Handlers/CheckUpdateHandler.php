<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;
use Illuminate\Support\Facades\Http;

class CheckUpdateHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        $currentVersion = config('app.version', '1.0.0');

        return [
            'success' => true,
            'message' => 'Check update command acknowledged.',
            'current_version' => $currentVersion,
            'needs_update' => true, // Dummy implementation
        ];
    }
}
