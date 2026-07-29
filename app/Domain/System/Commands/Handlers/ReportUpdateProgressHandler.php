<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;

class ReportUpdateProgressHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        return [
            'success' => true,
            'message' => 'Update progress reported.',
            'progress' => $payload['progress'] ?? 0,
        ];
    }
}
