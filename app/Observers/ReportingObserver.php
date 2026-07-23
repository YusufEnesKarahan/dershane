<?php

namespace App\Observers;

use App\Models\DashboardSnapshot;
use Illuminate\Support\Facades\Cache;

class ReportingObserver
{
    public function saved(DashboardSnapshot $snapshot)
    {
        Cache::forget('executive_dashboard_metrics');
    }
}
