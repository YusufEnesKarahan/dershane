<?php
namespace App\Observers;

use App\Models\Attendance;
use Illuminate\Support\Facades\Cache;

class AttendanceObserver
{
    public function saved(Attendance $attendance)
    {
        Cache::forget('attendance.analytics.summary');
    }
}
