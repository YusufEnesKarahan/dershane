<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQSystemInstance;
use App\Models\HQApiConnection;
use App\Domain\HQ\Services\HQMonitoringService;

class HQCentralController extends Controller
{
    public function __construct(
        protected HQMonitoringService $monitoringService
    ) {}

    public function index()
    {
        $metrics = $this->monitoringService->getDashboardMetrics();
        $instances = HQSystemInstance::with('tenant')->orderBy('last_seen_at', 'desc')->paginate(20);

        return view('admin.hq.index', compact('metrics', 'instances'));
    }
}
