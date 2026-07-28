<?php

namespace App\Domain\Platform\Services;

use App\Models\HQSchedulerLog;
use Exception;

class HQSchedulerService
{
    public function __construct(
        protected HQTelemetryService $telemetryService,
        protected HQHttpService $hqHttpService,
        protected HQSyncService $hqSyncService
    ) {}

    public function runTelemetry(): array
    {
        $snapshot = $this->telemetryService->createSnapshot();
        $this->telemetryService->storeSnapshot($snapshot, 'auto_scheduler');
        return $this->hqHttpService->sendTelemetry($snapshot);
    }

    public function runHeartbeat(): array
    {
        return $this->hqHttpService->ping();
    }

    public function processSyncQueue(): array
    {
        // For this foundation sprint, we only mock processing and log its attempt
        $pendingEvents = $this->hqSyncService->pending();
        
        return [
            'status' => 'mock_processed',
            'processed_count' => 0,
            'pending_count' => $pendingEvents,
            'message' => 'Sync queue processed by scheduler (foundation only)'
        ];
    }

    public function executeTask(string $taskName, callable $closure)
    {
        $startTime = microtime(true);
        $startedAt = now();
        
        $log = HQSchedulerLog::create([
            'task_name' => $taskName,
            'status' => 'success', // default to success, update on exception
            'started_at' => $startedAt,
        ]);

        try {
            $result = $closure();
            
            $log->update([
                'status' => 'success',
                'finished_at' => now(),
                'duration_ms' => round((microtime(true) - $startTime) * 1000),
                'result' => is_array($result) ? $result : ['output' => $result],
            ]);
            
            return true;
        } catch (Exception $e) {
            $log->update([
                'status' => 'failed',
                'finished_at' => now(),
                'duration_ms' => round((microtime(true) - $startTime) * 1000),
                'error_message' => $e->getMessage(),
                'result' => ['error' => true]
            ]);
            
            return false;
        }
    }
}
