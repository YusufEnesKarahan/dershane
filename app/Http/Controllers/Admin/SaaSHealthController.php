<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Platform\Services\SystemHealthService;
use App\Http\Controllers\Controller;

class SaaSHealthController extends Controller
{
    public function __construct(
        protected SystemHealthService $systemHealthService
    ) {}

    public function index()
    {
        $metrics = $this->systemHealthService->getDashboardMetrics();

        return view('admin.saas.system-health.index', compact('metrics'));
    }
}