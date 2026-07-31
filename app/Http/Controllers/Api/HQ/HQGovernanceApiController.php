<?php

namespace App\Http\Controllers\Api\HQ;

use App\Http\Controllers\Controller;
use App\Models\HQPolicy;
use App\Models\HQComplianceFramework;
use App\Models\HQTenant;
use App\Models\HQRiskScore;
use App\Models\HQSlaPolicy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HQGovernanceApiController extends Controller
{
    public function policies(Request $request): JsonResponse
    {
        $policies = HQPolicy::with('rules', 'assignments')->get();
        return response()->json(['status' => 'success', 'data' => $policies]);
    }

    public function compliance(Request $request): JsonResponse
    {
        $tenantId = $request->get('tenant_id');
        $query = \App\Models\HQComplianceResult::with('framework');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        return response()->json(['status' => 'success', 'data' => $query->get()]);
    }

    public function risk(Request $request): JsonResponse
    {
        $tenantId = $request->get('tenant_id');
        $query = HQRiskScore::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }
        return response()->json(['status' => 'success', 'data' => $query->latest('calculated_at')->get()]);
    }

    public function sla(Request $request): JsonResponse
    {
        $slas = HQSlaPolicy::with('violations')->get();
        return response()->json(['status' => 'success', 'data' => $slas]);
    }

    public function frameworks(Request $request): JsonResponse
    {
        $frameworks = HQComplianceFramework::with('controls')->get();
        return response()->json(['status' => 'success', 'data' => $frameworks]);
    }
}
