<?php

namespace App\Domain\HQ\Services;

use App\Models\HQCentralSyncLog;
use App\Models\HQSystemInstance;

class HQCommunicationService
{
    public function logSyncEvent(HQSystemInstance $instance, string $event, array $payload, array $response, string $status, int $durationMs = 0): void
    {
        HQCentralSyncLog::create([
            'system_instance_id' => $instance->id,
            'event' => $event,
            'payload' => $payload,
            'response' => $response,
            'status' => $status,
            'duration_ms' => $durationMs,
        ]);
    }
}
