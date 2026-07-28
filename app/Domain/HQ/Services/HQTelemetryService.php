<?php

namespace App\Domain\HQ\Services;

use App\Models\HQSystemInstance;
use App\Models\HQTelemetryRecord;

class HQTelemetryService
{
    public function processTelemetry(HQSystemInstance $instance, array $payload): HQTelemetryRecord
    {
        // Update last seen automatically since we got a payload
        $instance->update(['last_seen_at' => now(), 'status' => 'online']);

        return HQTelemetryRecord::create([
            'system_instance_id' => $instance->id,
            'type' => $payload['type'] ?? 'snapshot',
            'payload' => $payload,
        ]);
    }
}
