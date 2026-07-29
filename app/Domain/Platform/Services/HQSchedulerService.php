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
        // Process sync queue first
        $pendingEvents = $this->hqSyncService->pending();
        
        // Then process incoming remote commands
        $executor = app(\App\Domain\System\Commands\RemoteCommandExecutor::class);
        $commandResults = $executor->processPendingCommands();

        return [
            'status' => 'mock_processed_with_commands',
            'processed_count' => 0,
            'pending_count' => $pendingEvents,
            'commands' => $commandResults,
            'message' => 'Sync queue and remote commands processed'
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
