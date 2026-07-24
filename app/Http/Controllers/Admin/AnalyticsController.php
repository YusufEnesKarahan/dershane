<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\HR\Services\HRAnalyticsService;
use App\Domain\HR\Services\HRService;

class AnalyticsController extends Controller
{
    public function __construct(
        protected HRAnalyticsService $analyticsService,
        protected HRService $hrService
    ) {}

    public function dashboard()
    {
        $analytics = $this->analyticsService->getDashboardStats();
        return view('admin.hr.dashboard', compact('analytics'));
    }

    public function index()
    {
        $report = $this->analyticsService->getAnalyticsReport();
        return view('admin.hr.analytics', compact('report'));
    }
}
