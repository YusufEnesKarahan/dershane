<?php

namespace App\Observers;

use App\Models\StudentAdmission;
use Illuminate\Support\Facades\Cache;

class AdmissionObserver
{
    public function saved(StudentAdmission $admission)
    {
        Cache::forget('executive_dashboard_metrics');
    }
}
