<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQWorkflow;
use App\Models\HQWorkflowRun;
use App\Domain\HQ\Services\Workflow\WorkflowEngineService;

class HQWorkflowApiController extends Controller
{
    protected WorkflowEngineService $engine;

    public function __construct(WorkflowEngineService $engine)
    {
        $this->engine = $engine;
    }

    /**
     * List all workflows.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => HQWorkflow::with('steps')->get()
        ]);
    }

    /**
     * Get workflow details.
     */
    public function show(HQWorkflow $workflow)
    {
        return response()->json([
            'status' => 'success',
            'data' => $workflow->load('steps')
        ]);
    }

    /**
     * List all workflow runs (history).
     */
    public function history()
    {
        return response()->json([
            'status' => 'success',
            'data' => HQWorkflowRun::with(['workflow', 'tenant', 'logs'])->orderByDesc('created_at')->take(50)->get()
        ]);
    }

    /**
     * Trigger a matching workflow by event name.
     */
    public function trigger(Request $request)
    {
        $request->validate([
            'event' => 'required|string',
            'payload' => 'array'
        ]);

        $this->engine->handleEvent($request->input('event'), $request->input('payload', []));

        return response()->json([
            'status' => 'success',
            'message' => 'Event emitted and relevant workflows triggered.'
        ]);
    }

    /**
     * Manually run a specific workflow.
     */
    public function run(Request $request)
    {
        $request->validate([
            'workflow_id' => 'required|exists:hq_workflows,id',
            'payload' => 'array',
        ]);

        $workflow = HQWorkflow::findOrFail($request->workflow_id);
        
        $run = $this->engine->startWorkflow($workflow, $request->input('payload', []));

        if (!$run) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to start workflow (maybe no steps defined).'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Workflow started.',
            'run_id' => $run->id
        ]);
    }
}
