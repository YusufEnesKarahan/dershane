<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Reporting\Services\ExecutiveDashboardService;
use App\Domain\Reporting\Actions\GenerateDashboardSnapshot;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    public function __construct(
        protected ExecutiveDashboardService $dashboardService,
        protected GenerateDashboardSnapshot $snapshotAction
    ) {}

    public function index()
    {
        $metrics = $this->dashboardService->getMetrics();
        return view('admin.reporting.dashboard', compact('metrics'));
    }

    public function analytics()
    {
        $metrics = $this->dashboardService->getMetrics();
        return view('admin.reporting.analytics', compact('metrics'));
    }

    public function snapshot()
    {
        $this->snapshotAction->execute();
        return redirect()->back()->with('success', 'Dashboard anlık görüntüsü (snapshot) başarıyla kaydedildi.');
    }
}
