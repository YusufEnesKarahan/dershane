<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQDeployment;
use App\Models\HQReleaseChannel;
use App\Domain\HQ\Services\Fleet\DeploymentService;

class HQDeploymentApiController extends Controller
{
    protected DeploymentService $deploymentService;

    public function __construct(DeploymentService $deploymentService)
    {
        $this->deploymentService = $deploymentService;
    }

    public function start(Request $request)
    {
        $request->validate([
            'version' => 'required|string',
            'type' => 'required|in:manual,rolling,canary,blue-green,staged',
            'rollout_percentage' => 'nullable|integer|min:0|max:100',
            'target_tenant_ids' => 'nullable|array',
            'target_group_ids' => 'nullable|array',
        ]);

        $deployment = HQDeployment::create([
            'version' => $request->version,
            'type' => $request->type,
            'rollout_percentage' => $request->rollout_percentage ?? 0,
            'status' => 'queued',
        ]);

        // Create targets (simplified for now, ideally resolving groups to tenants)
        if ($request->has('target_tenant_ids')) {
            foreach ($request->target_tenant_ids as $tenantId) {
                $deployment->targets()->create([
                    'targetable_type' => \App\Models\HQTenant::class,
                    'targetable_id' => $tenantId,
                ]);
            }
        }

        $this->deploymentService->startDeployment($deployment);

        return response()->json([
            'status' => 'success',
            'message' => 'Deployment started',
            'deployment_id' => $deployment->id
        ]);
    }

    public function report(Request $request)
    {
        $request->validate([
            'deployment_id' => 'required|exists:hq_deployments,id',
            'target_id' => 'required|exists:hq_deployment_targets,id',
            'status' => 'required|in:success,failed',
            'error' => 'nullable|string',
        ]);

        $target = \App\Models\HQDeploymentTarget::findOrFail($request->target_id);
        
        $this->deploymentService->completeTarget(
            $target, 
            $request->status === 'success', 
            $request->error
        );

        return response()->json(['status' => 'success']);
    }

    public function status(Request $request)
    {
        $request->validate([
            'deployment_id' => 'required|exists:hq_deployments,id',
        ]);

        $deployment = HQDeployment::with('targets')->findOrFail($request->deployment_id);

        return response()->json([
            'status' => 'success',
            'data' => $deployment
        ]);
    }

    public function releaseChannels()
    {
        $channels = HQReleaseChannel::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $channels
        ]);
    }
}
