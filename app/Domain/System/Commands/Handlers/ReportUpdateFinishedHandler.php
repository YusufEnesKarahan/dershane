<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;

class ReportUpdateFinishedHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        return [
            'success' => true,
            'message' => 'Update finished reported.',
            'update_success' => $payload['success'] ?? true,
        ];
    }
}
