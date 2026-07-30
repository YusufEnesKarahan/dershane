<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HQWorkflowRun;
use App\Models\HQWorkflowStep;
use App\Domain\HQ\Services\Workflow\WorkflowExecutionService;

class ProcessWorkflowStepJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public HQWorkflowRun $run;
    public HQWorkflowStep $step;

    public $tries = 3; // Retry support
    public $backoff = [10, 30, 60]; // Exponential backoff

    /**
     * Create a new job instance.
     */
    public function __construct(HQWorkflowRun $run, HQWorkflowStep $step)
    {
        $this->run = $run;
        $this->step = $step;
    }

    /**
     * Execute the job.
     */
    public function handle(WorkflowExecutionService $executionService): void
    {
        $executionService->executeStep($this->run, $this->step);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        // Mark workflow as failed if it runs out of retries
        app(WorkflowExecutionService::class)->finishRun(
            $this->run,
            'failed',
            'Queue Job Failed: ' . $exception->getMessage()
        );
    }
}
