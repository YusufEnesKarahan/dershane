<?php

namespace App\Jobs;

use App\Models\HQProvisioningTask;
use App\Domain\Onboarding\Services\TenantProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function handle(TenantProvisioningService $provisioningService): void
    {
        $task = HQProvisioningTask::find($this->taskId);
        if (!$task) return;

        try {
            $task->update(['status' => 'processing']);

            // Execute provisioning
            $provisioningService->executeTask($task);

            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            event(new \App\Events\ProvisioningCompleted($task->tenant, $task));
        } catch (Throwable $e) {
            Log::error("ProvisionTenantJob failed: " . $e->getMessage());
            $task->update(['status' => 'failed', 'payload' => array_merge($task->payload ?? [], ['error' => $e->getMessage()])]);
            event(new \App\Events\ProvisioningFailed($task->tenant, $task, $e->getMessage()));
            throw $e;
        }
    }
}
