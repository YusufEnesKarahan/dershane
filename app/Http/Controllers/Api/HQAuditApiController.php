<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQAuditService;
use App\Models\HQSystemInstance;

class HQAuditApiController extends Controller
{
    public function report(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|string',
            'action' => 'required|string',
            'category' => 'required|string',
            'severity' => 'nullable|string',
            'description' => 'nullable|string',
            'old_values' => 'nullable|array',
            'new_values' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        $instance = HQSystemInstance::where('system_uuid', $validated['system_id'])->first();

        HQAuditService::logSystemAction(
            action: $validated['action'],
            category: $validated['category'],
            severity: $validated['severity'] ?? 'info',
            description: $validated['description'] ?? 'Reported by remote ERP instance.',
            tenantId: $instance ? $instance->tenant_id : null,
            systemInstanceId: $instance ? $instance->id : null,
            metadata: $validated['metadata'] ?? []
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Audit log recorded successfully.',
            'timestamp' => now()->timestamp,
        ]);
    }
}
