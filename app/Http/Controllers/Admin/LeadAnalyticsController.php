<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\CRM\Services\LeadAnalyticsService;
use Illuminate\Http\Request;

class LeadAnalyticsController extends Controller
{
    public function __construct(protected LeadAnalyticsService $analyticsService) {}

    public function index()
    {
        $analytics = $this->analyticsService->getAnalyticsSummary();
        return view('admin.crm.analytics', compact('analytics'));
    }
}
