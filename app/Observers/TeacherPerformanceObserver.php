<?php
namespace App\Observers;

use App\Models\TeacherPerformance;

class TeacherPerformanceObserver
{
    public function saved(TeacherPerformance $perf)
    {
        // Performance logs
    }
}
