<?php

namespace App\Domain\HQ\Services\Workflow;

use App\Models\HQWorkflow;
use App\Models\HQWorkflowRun;
use App\Models\HQTenant;
use Illuminate\Support\Facades\Log;

class WorkflowEngineService
{
    protected WorkflowExecutionService $executionService;
    protected WorkflowConditionService $conditionService;

    public function __construct(
        WorkflowExecutionService $executionService,
        WorkflowConditionService $conditionService
    ) {
        $this->executionService = $executionService;
        $this->conditionService = $conditionService;
    }

    /**
     * Handle an event triggered in the system.
     */
    public function handleEvent(string $eventName, array $payload, ?HQTenant $tenant = null)
    {
        $workflows = HQWorkflow::where('trigger_event', $eventName)
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            // Check initial trigger conditions if present
            if ($workflow->trigger_conditions) {
                if (!$this->conditionService->evaluateGroups($workflow->trigger_conditions, $payload)) {
                    continue; // Skip this workflow
                }
            }

            $this->startWorkflow($workflow, $payload, $tenant);
        }
    }

    /**
     * Start a new workflow run.
     */
    public function startWorkflow(HQWorkflow $workflow, array $payload, ?HQTenant $tenant = null): ?HQWorkflowRun
    {
        $initialStep = $workflow->getInitialStep();
        
        if (!$initialStep) {
            Log::warning("WorkflowEngine: Workflow {$workflow->slug} has no steps.");
            return null;
        }

        $run = HQWorkflowRun::create([
            'hq_workflow_id' => $workflow->id,
            'tenant_id' => $tenant?->id,
            'current_step_id' => $initialStep->id,
            'status' => 'pending',
            'payload' => $payload,
        ]);

        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'workflow.start',
            category: 'workflow',
            severity: 'info',
            description: "Workflow {$workflow->name} started.",
            tenantId: $tenant?->id,
            metadata: ['workflow_id' => $workflow->id, 'run_id' => $run->id]
        );

        // Start execution asynchronously
        $this->executionService->dispatchStep($run, $initialStep);

        return $run;
    }
}
