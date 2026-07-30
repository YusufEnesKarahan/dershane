<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQWorkflow;
use App\Models\HQWorkflowRun;

class HQWorkflowController extends Controller
{
    /**
     * Display a listing of workflows.
     */
    public function index()
    {
        $workflows = HQWorkflow::withCount('runs')->latest()->get();
        return view('admin.hq.workflows.index', compact('workflows'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create()
    {
        return view('admin.hq.workflows.create');
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:hq_workflows',
            'trigger_event' => 'required|string',
            'steps_json' => 'required|json'
        ]);

        $workflow = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $workflow = HQWorkflow::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'trigger_event' => $request->trigger_event,
                'is_active' => $request->has('is_active'),
            ]);

            $stepsData = json_decode($request->steps_json, true);
            $previousStep = null;
            $stepModels = [];

            // We need to create steps and link them
            foreach ($stepsData as $index => $stepData) {
                $step = \App\Models\HQWorkflowStep::create([
                    'hq_workflow_id' => $workflow->id,
                    'type' => $stepData['type'],
                    'name' => $stepData['name'],
                    'config' => $stepData['config'] ?? [],
                    'order_index' => $index,
                ]);
                $stepModels[] = $step;
                
                // Sequential linking for simplicity in JSON builder
                if ($previousStep) {
                    $previousStep->update(['next_step_id' => $step->id]);
                }
                $previousStep = $step;
            }

            return $workflow;
        });

        return redirect()->route('admin.platform.hq_central.workflows.index')
            ->with('success', 'Workflow created successfully.');
    }

    /**
     * Display the specified workflow details.
     */
    public function show(HQWorkflow $workflow)
    {
        $workflow->load('steps');
        $recentRuns = $workflow->runs()->with('tenant')->latest()->take(10)->get();
        
        return view('admin.hq.workflows.show', compact('workflow', 'recentRuns'));
    }

    /**
     * Workflow history (runs).
     */
    public function history()
    {
        $runs = HQWorkflowRun::with(['workflow', 'tenant'])->latest()->paginate(20);
        return view('admin.hq.workflows.history', compact('runs'));
    }
}
