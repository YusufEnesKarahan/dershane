<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\UsageSynchronizationService;
use App\Domain\HQ\Services\HQEntitlementService;
use App\Models\HQUsageSnapshot;

class HQUsageApiController extends Controller
{
    public function __construct(
        protected UsageSynchronizationService $syncService,
        protected HQEntitlementService $entitlementService
    ) {}

    /**
     * POST /api/hq/usage/report
     * Receive batched telemetry metrics from an ERP instance.
     */
    public function report(Request $request)
    {
        $instance = HQSystemInstance::where('system_uuid', $request->system_id)->first();
        if (!$instance || !$instance->tenant) {
            return response()->json(['status' => 'error', 'message' => 'System or Tenant not found'], 404);
        }

        $result = $this->syncService->processIncomingReport($instance->tenant, $request->all());

        return response()->json($result, $result['status'] === 'error' ? 400 : 200);
    }

    /**
     * GET /api/hq/quota
     * Return active quota rules/limits for the instance to self-enforce.
     */
    public function quota(Request $request)
    {
        $instance = HQSystemInstance::where('system_uuid', $request->system_id)->first();
        if (!$instance || !$instance->tenant) {
            return response()->json(['status' => 'error', 'message' => 'System or Tenant not found'], 404);
        }

        $limits = $this->entitlementService->getLimits($instance->tenant);

        return response()->json([
            'status' => 'success',
            'quotas' => $limits,
        ]);
    }

    /**
     * GET /api/hq/usage/history
     * Get historical aggregated usage.
     */
    public function history(Request $request)
    {
        $instance = HQSystemInstance::where('system_uuid', $request->system_id)->first();
        if (!$instance || !$instance->tenant) {
            return response()->json(['status' => 'error', 'message' => 'System or Tenant not found'], 404);
        }

        $period = $request->get('period', 'daily');
        
        $history = HQUsageSnapshot::where('tenant_id', $instance->tenant->id)
            ->where('period', $period)
            ->orderByDesc('period_start')
            ->limit(30)
            ->get();

        return response()->json([
            'status' => 'success',
            'history' => $history,
        ]);
    }
}
