<?php

namespace App\Domain\Onboarding\Services;

use App\Models\Institution;
use App\Models\HQProvisioningTask;

class ProvisioningTaskService
{
    public function createTask(Institution $tenant, string $taskType, array $payload = []): HQProvisioningTask
    {
        $task = HQProvisioningTask::create([
            'tenant_id' => $tenant->id,
            'task_type' => $taskType,
            'status' => 'pending',
            'payload' => $payload,
        ]);

        event(new \App\Events\ProvisioningStarted($tenant, $task));
        
        \App\Jobs\ProvisionTenantJob::dispatch($task->id);

        return $task;
    }

    public function getTaskStatus(string $uuid): ?HQProvisioningTask
    {
        return HQProvisioningTask::where('uuid', $uuid)->first();
    }
}
