<?php

namespace App\Domain\Platform\Services;

use App\Models\HQSyncEvent;
use Illuminate\Support\Facades\Cache;

class HQSyncService
{
    /**
     * Build the standard payload format.
     */
    public function buildPayload(string $eventType, array $data = []): array
    {
        return [
            'event' => $eventType,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];
    }

    /**
     * Queue a new sync event.
     */
    public function queue(string $eventType, array $payload = []): HQSyncEvent
    {
        return HQSyncEvent::create([
            'event_type' => $eventType,
            'payload' => $this->buildPayload($eventType, $payload),
            'status' => 'pending',
        ]);
    }

    /** Helper Methods */

    public function queueLicenseChanged(array $licenseData)
    {
        return $this->queue('license.changed', $licenseData);
    }

    public function queueFeatureChanged(string $featureName, bool $enabled)
    {
        return $this->queue('feature.changed', ['feature' => $featureName, 'enabled' => $enabled]);
    }

    public function queueVersionChanged(string $newVersion)
    {
        return $this->queue('version.changed', ['version' => $newVersion]);
    }

    public function queueBranchCreated(int $branchId, string $branchName)
    {
        return $this->queue('branch.created', ['id' => $branchId, 'name' => $branchName]);
    }

    public function queueBranchUpdated(int $branchId, array $changes)
    {
        return $this->queue('branch.updated', ['id' => $branchId, 'changes' => $changes]);
    }

    public function queueUserCreated(int $userId, string $role)
    {
        return $this->queue('user.created', ['id' => $userId, 'role' => $role]);
    }

    public function queueUserUpdated(int $userId, array $changes)
    {
        return $this->queue('user.updated', ['id' => $userId, 'changes' => $changes]);
    }

    /**
     * Retry a failed event.
     */
    public function retry(int $id): bool
    {
        $event = HQSyncEvent::find($id);

        if ($event && in_array($event->status, ['failed', 'processing'])) {
            $event->update([
                'status' => 'pending',
                'retry_count' => $event->retry_count + 1,
                'last_error' => null
            ]);
            return true;
        }

        return false;
    }

    /** Retrieve Methods */

    public function pending()
    {
        return Cache::remember('hq_sync_pending_count', now()->addMinutes(5), function () {
            return HQSyncEvent::where('status', 'pending')->count();
        });
    }

    public function completed()
    {
        return Cache::remember('hq_sync_completed_count', now()->addMinutes(5), function () {
            return HQSyncEvent::where('status', 'completed')->count();
        });
    }

    public function failed()
    {
        return Cache::remember('hq_sync_failed_count', now()->addMinutes(5), function () {
            return HQSyncEvent::where('status', 'failed')->count();
        });
    }
}
