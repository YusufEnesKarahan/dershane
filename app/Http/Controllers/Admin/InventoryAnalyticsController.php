<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\InventoryAnalyticsService;

class InventoryAnalyticsController extends Controller
{
    public function __construct(
        protected InventoryAnalyticsService $analyticsService
    ) {}

    public function dashboard()
    {
        $analytics = $this->analyticsService->getDashboardStats();
        return view('admin.inventory.dashboard', compact('analytics'));
    }

    public function index()
    {
        $report = $this->analyticsService->getAnalyticsReport();
        return view('admin.inventory.analytics', compact('report'));
    }
}
