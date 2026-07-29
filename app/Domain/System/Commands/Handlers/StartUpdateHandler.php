<?php

namespace App\Domain\System\Commands\Handlers;

use App\Domain\System\Commands\RemoteCommandHandlerInterface;

class StartUpdateHandler implements RemoteCommandHandlerInterface
{
    public function handle(array $payload): array
    {
        // Does NOT update. Only records that update has started.
        // In reality, this might trigger a local queue job or background process
        // that handles the actual download/extraction.
        
        return [
            'success' => true,
            'message' => 'Update process started successfully.',
            'job_id' => $payload['job_id'] ?? null,
            'version' => $payload['version'] ?? null,
        ];
    }
}
