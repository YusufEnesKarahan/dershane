<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQAlert;
use App\Domain\HQ\Services\HQAlertService;
use App\Domain\HQ\Services\HQMonitoringService;
use Illuminate\Support\Facades\Gate;

class HQAlertController extends Controller
{
    protected HQAlertService $alertService;

    public function __construct(HQAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        Gate::authorize('hq.viewAlerts');

        $query = HQAlert::with(['tenant', 'systemInstance', 'rule'])->latest('triggered_at');

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('date_start')) {
            $query->where('triggered_at', '>=', $request->date_start);
        }

        $alerts = $query->paginate(20);
        $statistics = $this->alertService->getStatistics();

        return view('admin.hq.alerts.index', compact('alerts', 'statistics'));
    }

    public function show(HQAlert $alert)
    {
        Gate::authorize('hq.viewAlerts');
        
        $alert->load(['tenant', 'systemInstance', 'rule', 'notificationLogs']);
        
        return view('admin.hq.alerts.show', compact('alert'));
    }

    public function acknowledge(HQAlert $alert)
    {
        Gate::authorize('hq.manageAlerts');
        
        $this->alertService->acknowledgeAlert($alert);
        
        return back()->with('success', 'Alert acknowledged successfully.');
    }

    public function resolve(HQAlert $alert)
    {
        Gate::authorize('hq.manageAlerts');
        
        $this->alertService->resolveAlert($alert);
        
        return back()->with('success', 'Alert resolved successfully.');
    }
}
