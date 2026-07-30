<?php

namespace App\Domain\HQ\Services\Workflow;

use App\Models\HQWorkflowRun;
use App\Models\HQWorkflowStep;
use App\Models\HQWorkflowExecution;
use App\Models\HQWorkflowLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkflowExecutionService
{
    protected WorkflowConditionService $conditionService;
    protected WorkflowActionService $actionService;

    public function __construct(
        WorkflowConditionService $conditionService,
        WorkflowActionService $actionService
    ) {
        $this->conditionService = $conditionService;
        $this->actionService = $actionService;
    }

    /**
     * Dispatch a step to the queue.
     */
    public function dispatchStep(HQWorkflowRun $run, HQWorkflowStep $step)
    {
        $run->update(['status' => 'running', 'started_at' => $run->started_at ?? now()]);

        // Instead of executing immediately, we dispatch a job.
        \App\Jobs\ProcessWorkflowStepJob::dispatch($run, $step);
    }

    /**
     * Execute a workflow step.
     */
    public function executeStep(HQWorkflowRun $run, HQWorkflowStep $step)
    {
        if ($run->status === 'failed' || $run->status === 'timeout' || $run->status === 'cancelled') {
            return;
        }

        $execution = HQWorkflowExecution::create([
            'hq_workflow_run_id' => $run->id,
            'hq_workflow_step_id' => $step->id,
            'status' => 'running',
            'started_at' => now(),
            'input_data' => $run->payload,
        ]);

        $this->log($run, $execution, 'info', "Starting step: {$step->name} ({$step->type})");

        try {
            $nextStep = null;

            if ($step->type === 'condition') {
                $result = $this->conditionService->evaluateGroups($step->config['conditions'] ?? [], $run->payload);
                $execution->update(['output_data' => ['result' => $result]]);
                $nextStep = $result ? $step->nextStep : $step->fallbackStep;
            } elseif ($step->type === 'action') {
                $output = $this->actionService->execute($step->config, $run->payload, $run->tenant);
                // Actions might update the payload context
                if (is_array($output)) {
                    $newPayload = array_merge($run->payload ?? [], $output);
                    $run->update(['payload' => $newPayload]);
                }
                $execution->update(['output_data' => $output]);
                $nextStep = $step->nextStep;
            } elseif ($step->type === 'delay') {
                $delayMinutes = $step->config['minutes'] ?? 0;
                $this->log($run, $execution, 'info', "Delaying for {$delayMinutes} minutes.");
                $execution->update(['status' => 'success', 'completed_at' => now()]);
                
                if ($step->nextStep) {
                    $run->update(['current_step_id' => $step->nextStep->id]);
                    \App\Jobs\ProcessWorkflowStepJob::dispatch($run, $step->nextStep)->delay(now()->addMinutes($delayMinutes));
                } else {
                    $this->finishRun($run, 'completed');
                }
                return; // Exit execution flow for delay
            }

            $execution->update(['status' => 'success', 'completed_at' => now()]);
            $this->log($run, $execution, 'info', "Completed step: {$step->name}");

            if ($nextStep) {
                $run->update(['current_step_id' => $nextStep->id]);
                $this->dispatchStep($run, $nextStep);
            } else {
                $this->finishRun($run, 'completed');
            }

        } catch (\Exception $e) {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            $this->log($run, $execution, 'error', "Step failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            $run->increment('retry_count');
            
            // Critical alert on failure
            app(\App\Domain\HQ\Services\HQAlertService::class)->createAlert(
                $run->tenant,
                'workflow.failed',
                'critical',
                "Workflow '{$run->workflow->name}' failed at step '{$step->name}'.",
                ['error' => $e->getMessage(), 'run_id' => $run->id]
            );

            $this->finishRun($run, 'failed', $e->getMessage());
        }
    }

    public function finishRun(HQWorkflowRun $run, string $status, ?string $error = null)
    {
        $run->update([
            'status' => $status,
            'completed_at' => now(),
            'error_message' => $error,
        ]);
        
        $this->log($run, null, $status === 'completed' ? 'info' : 'error', "Workflow run finished with status: {$status}");
        
        app(\App\Domain\HQ\Services\HQAuditService::class)->logSystemAction(
            action: 'workflow.' . $status,
            category: 'workflow',
            severity: $status === 'completed' ? 'info' : 'error',
            description: "Workflow finished: {$status}",
            tenantId: $run->tenant?->id,
            metadata: ['run_id' => $run->id]
        );
    }

    protected function log(HQWorkflowRun $run, ?HQWorkflowExecution $execution, string $level, string $message, array $context = [])
    {
        HQWorkflowLog::create([
            'hq_workflow_run_id' => $run->id,
            'hq_workflow_execution_id' => $execution?->id,
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
