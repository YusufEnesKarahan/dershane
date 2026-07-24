<?php

namespace App\Observers;

use App\Models\Lead;
use Illuminate\Support\Facades\Cache;

class LeadObserver
{
    public function saved(Lead $lead)
    {
        Cache::forget('executive_dashboard_metrics');
    }
}
