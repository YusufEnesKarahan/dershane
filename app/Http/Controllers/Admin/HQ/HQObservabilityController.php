<?php

namespace App\Http\Controllers\Admin\HQ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQAuditService;

class HQObservabilityController extends Controller
{
    public function index()
    {
        return view('admin.hq.observability.index'); // dummy view
    }

    public function updateSettings(Request $request, HQAuditService $auditService)
    {
        // ... logic to update config ...

        $auditService->log(
            'configuration_changed',
            'Observability configuration updated.',
            ['tenant_id' => null, 'user_id' => auth()->id()]
        );

        return redirect()->back()->with('success', 'Settings updated.');
    }

    public function disableMonitoring(Request $request, HQAuditService $auditService)
    {
        // ... disable logic ...

        $auditService->log(
            'monitoring_disabled',
            'Monitoring has been disabled.',
            ['tenant_id' => null, 'user_id' => auth()->id(), 'severity' => 'high']
        );

        return redirect()->back()->with('warning', 'Monitoring disabled.');
    }

    public function updateSecurityPolicy(Request $request, HQAuditService $auditService)
    {
        // ... policy logic ...

        $auditService->log(
            'security_policy_changed',
            'Security policy changed.',
            ['tenant_id' => null, 'user_id' => auth()->id()]
        );

        return redirect()->back()->with('success', 'Security policy updated.');
    }

    public function updateMetricThreshold(Request $request, HQAuditService $auditService)
    {
        // ... threshold logic ...

        $auditService->log(
            'metric_threshold_changed',
            'Metric threshold updated.',
            ['tenant_id' => null, 'user_id' => auth()->id()]
        );

        return redirect()->back()->with('success', 'Metric threshold updated.');
    }
}
