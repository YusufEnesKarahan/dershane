<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Services\HQAlertRuleEvaluator;
use Illuminate\Support\Facades\Log;

class HQAlertApiController extends Controller
{
    protected HQAlertRuleEvaluator $evaluator;

    public function __construct(HQAlertRuleEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    public function report(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|string',
            'type' => 'required|string',
            'message' => 'nullable|string',
            'severity' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $instance = HQSystemInstance::where('system_uuid', $validated['system_id'])->first();

        if (!$instance) {
            Log::warning("HQAlertApi: Received alert from unknown system UUID: {$validated['system_id']}");
        }

        $context = [
            'type' => $validated['type'],
            'tenant_id' => $instance ? $instance->tenant_id : null,
            'system_instance_id' => $instance ? $instance->id : null,
            'message' => $validated['message'] ?? 'Alert reported by remote instance',
            'severity' => $validated['severity'] ?? 'warning',
            'metadata' => $validated['metadata'] ?? [],
        ];

        // Evaluate the event. We'll use a generic external event type for these unless specified
        $eventType = "remote.{$validated['type']}";
        $this->evaluator->evaluateEvent($eventType, $context);

        // Fallback: If no rule caught it but it was explicitly reported as an alert by ERP,
        // we might want a "catch-all" rule for remote alerts. For now, the evaluator handles it 
        // if a rule matches event_type or generic JSON type matches.

        return response()->json([
            'status' => 'success',
            'message' => 'Alert reported and evaluated successfully.',
            'timestamp' => now()->timestamp,
        ]);
    }
}
