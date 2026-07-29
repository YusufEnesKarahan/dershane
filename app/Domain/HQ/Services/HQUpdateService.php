<?php

namespace App\Domain\HQ\Services;

use App\Models\HQUpdateJob;
use App\Models\HQVersion;
use App\Models\HQSystemInstance;
use App\Models\HQTenant;
use App\Domain\HQ\Enums\HQCommandType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HQUpdateService
{
    public function __construct(
        protected HQRemoteCommandService $commandService
    ) {}

    /**
     * Create an update job and dispatch start command for a single instance.
     */
    public function dispatchUpdateToInstance(HQSystemInstance $instance, HQVersion $version): HQUpdateJob
    {
        return DB::transaction(function () use ($instance, $version) {
            $job = HQUpdateJob::create([
                'version_id' => $version->id,
                'system_instance_id' => $instance->id,
                'target_type' => 'single',
                'status' => 'scheduled',
                'scheduled_at' => now(),
            ]);

            // Dispatch remote command
            $this->commandService->dispatchCommand(
                $instance, 
                HQCommandType::START_UPDATE, 
                ['job_id' => $job->id, 'version' => $version->version],
                99 // High priority
            );

            return $job;
        });
    }

    /**
     * Create an update job and dispatch start commands for a tenant.
     */
    public function dispatchUpdateToTenant(HQTenant $tenant, HQVersion $version): HQUpdateJob
    {
        return DB::transaction(function () use ($tenant, $version) {
            $job = HQUpdateJob::create([
                'version_id' => $version->id,
                'tenant_id' => $tenant->id,
                'target_type' => 'tenant',
                'status' => 'scheduled',
                'scheduled_at' => now(),
            ]);

            // Dispatch commands to all instances of this tenant
            $this->commandService->dispatchToTenant(
                $tenant, 
                HQCommandType::START_UPDATE, 
                ['job_id' => $job->id, 'version' => $version->version],
                99
            );

            return $job;
        });
    }

    /**
     * Create an update job and dispatch start commands for ALL instances globally.
     */
    public function dispatchUpdateGlobal(HQVersion $version): HQUpdateJob
    {
        return DB::transaction(function () use ($version) {
            $job = HQUpdateJob::create([
                'version_id' => $version->id,
                'target_type' => 'global',
                'status' => 'scheduled',
                'scheduled_at' => now(),
            ]);

            // Note: In a massive system, this should probably be chunked or pushed to a queue.
            // For now, doing it inline as per current orchestrator capabilities.
            HQSystemInstance::where('status', 'online')->chunk(100, function ($instances) use ($version, $job) {
                foreach ($instances as $instance) {
                    $this->commandService->dispatchCommand(
                        $instance, 
                        HQCommandType::START_UPDATE, 
                        ['job_id' => $job->id, 'version' => $version->version],
                        99
                    );
                }
            });

            return $job;
        });
    }

    /**
     * Cancel an update job.
     */
    public function cancelUpdate(HQUpdateJob $job): HQUpdateJob
    {
        $job->update([
            'status' => 'cancelled',
            'error_message' => 'Cancelled by HQ Administrator'
        ]);
        return $job;
    }

    /**
     * Retry a failed or cancelled update job.
     */
    public function retryUpdate(HQUpdateJob $job): HQUpdateJob
    {
        // For simplicity, we just dispatch the START_UPDATE again for single target.
        // If it's tenant or global, we would need to determine which ones failed.
        // For this sprint's scope, we will retry the same target type.

        return DB::transaction(function () use ($job) {
            $job->update([
                'status' => 'scheduled',
                'progress' => 0,
                'result' => null,
                'error_message' => null,
                'scheduled_at' => now(),
                'started_at' => null,
                'completed_at' => null,
            ]);

            if ($job->target_type === 'single') {
                $this->commandService->dispatchCommand(
                    $job->systemInstance, 
                    HQCommandType::START_UPDATE, 
                    ['job_id' => $job->id, 'version' => $job->version->version],
                    99
                );
            } elseif ($job->target_type === 'tenant') {
                $this->commandService->dispatchToTenant(
                    $job->tenant, 
                    HQCommandType::START_UPDATE, 
                    ['job_id' => $job->id, 'version' => $job->version->version],
                    99
                );
            } elseif ($job->target_type === 'global') {
                HQSystemInstance::where('status', 'online')->chunk(100, function ($instances) use ($job) {
                    foreach ($instances as $instance) {
                        $this->commandService->dispatchCommand(
                            $instance, 
                            HQCommandType::START_UPDATE, 
                            ['job_id' => $job->id, 'version' => $job->version->version],
                            99
                        );
                    }
                });
            }

            return $job;
        });
    }

    /**
     * Record progress from ERP.
     */
    public function recordProgress(int $jobId, int $progress): ?HQUpdateJob
    {
        $job = HQUpdateJob::find($jobId);
        if (!$job) return null;

        if ($job->status !== 'sent') {
            $job->status = 'sent'; // Indicate it has started executing
            if (!$job->started_at) {
                $job->started_at = now();
            }
        }
        
        $job->progress = $progress;
        $job->save();

        return $job;
    }

    /**
     * Record finished from ERP.
     */
    public function recordFinished(int $jobId, bool $success, ?string $message = null, ?array $result = null): ?HQUpdateJob
    {
        $job = HQUpdateJob::find($jobId);
        if (!$job) return null;

        $job->status = $success ? 'completed' : 'failed';
        $job->progress = $success ? 100 : $job->progress;
        $job->completed_at = now();
        $job->result = $result;
        
        if (!$success) {
            $job->error_message = $message;
        }

        $job->save();

        return $job;
    }
}
