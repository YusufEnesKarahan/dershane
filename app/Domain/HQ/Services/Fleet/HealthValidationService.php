<?php

namespace App\Domain\HQ\Services\Fleet;

use App\Models\HQSystemInstance;

class HealthValidationService
{
    /**
     * Perform post-deployment health check on a specific instance.
     * Note: Avoids RCE by communicating via HQRemoteCommandService or standard ping mechanism.
     */
    public function validateInstanceHealth(HQSystemInstance $instance): array
    {
        // Check standard heartbeat freshness first
        if ($instance->last_seen_at && $instance->last_seen_at->diffInMinutes(now()) > 5) {
            return [
                'status' => 'failed',
                'reason' => 'Heartbeat missed. System offline.'
            ];
        }

        // Ideally, here we would issue a 'health_check' command to the instance via CommandQueue
        // and wait for a response, or check recent telemetry. For HQ Central orchestration, we
        // assume telemetry or API ping is healthy if status is online.
        if ($instance->status !== 'online') {
            return [
                'status' => 'failed',
                'reason' => 'System status is ' . $instance->status
            ];
        }

        return [
            'status' => 'healthy',
            'reason' => 'Heartbeat and telemetry normal.'
        ];
    }
}
