<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\HQ\Services\Observability\HQLoggingService;
use App\Domain\HQ\Services\Observability\HQMetricService;
use App\Domain\HQ\Services\Observability\HQTracingService;
use App\Domain\HQ\Services\Observability\HealthMonitoringService;
use App\Domain\HQ\Services\HQEntitlementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HQObservabilityApiController extends Controller
{
    /**
     * POST /api/hq/logs
     */
    public function storeLog(Request $request, HQLoggingService $loggingService, HQEntitlementService $entitlementService): JsonResponse
    {
        $validated = $request->validate([
            'level' => 'required|string|in:debug,info,warning,error,critical',
            'message' => 'required|string',
            'context' => 'nullable|array',
            'service' => 'nullable|string',
        ]);

        $tenantId = $request->attributes->get('tenant_id');
        
        if ($tenantId && !$entitlementService->checkFeature($tenantId, 'monitoring_features')) {
            return response()->json(['status' => 'error', 'message' => 'Monitoring feature is not included in your plan'], 403);
        }

        $loggingService->log(
            $validated['level'],
            $validated['message'],
            $validated['context'] ?? [],
            $tenantId,
            $validated['service'] ?? 'unknown'
        );

        return response()->json(['status' => 'success', 'message' => 'Log queued']);
    }

    /**
     * POST /api/hq/metrics
     */
    public function storeMetric(Request $request, HQMetricService $metricService): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|string|in:counter,gauge,timing',
            'value' => 'required|numeric',
            'unit' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $tenantId = $request->attributes->get('tenant_id');

        $metricService->recordMetric(
            $validated['name'],
            $validated['type'],
            (float) $validated['value'],
            $validated['unit'] ?? null,
            $validated['tags'] ?? [],
            $tenantId
        );

        return response()->json(['status' => 'success', 'message' => 'Metric queued']);
    }

    /**
     * POST /api/hq/traces
     */
    public function storeTrace(Request $request, HQTracingService $tracingService): JsonResponse
    {
        $validated = $request->validate([
            'trace_id' => 'required|string',
            'service_name' => 'required|string',
            'operation' => 'required|string',
            'duration_ms' => 'required|integer',
            'status' => 'required|string|in:success,error',
            'metadata' => 'nullable|array',
        ]);

        $tenantId = $request->attributes->get('tenant_id');
        
        $traceData = [
            'trace_id' => $validated['trace_id'],
            'tenant_id' => $tenantId,
            'service_name' => $validated['service_name'],
            'operation' => $validated['operation'],
            'duration_ms' => $validated['duration_ms'],
            'status' => $validated['status'],
            'metadata' => $validated['metadata'] ?? [],
        ];

        \App\Jobs\ProcessObservabilityTraceJob::dispatch($traceData);

        return response()->json(['status' => 'success', 'message' => 'Trace queued']);
    }

    /**
     * GET /api/hq/health
     */
    public function checkHealth(Request $request, HealthMonitoringService $healthService): JsonResponse
    {
        $tenantId = $request->attributes->get('tenant_id');
        
        // This will perform synchronous checks and save them to DB
        $results = $healthService->checkAll($tenantId);

        $hasError = false;
        foreach ($results as $check) {
            if ($check->status === 'critical') {
                $hasError = true;
                break;
            }
        }

        return response()->json([
            'status' => $hasError ? 'degraded' : 'healthy',
            'checks' => $results
        ], $hasError ? 503 : 200);
    }
}
