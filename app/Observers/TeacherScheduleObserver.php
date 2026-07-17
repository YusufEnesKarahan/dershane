<?php
namespace App\Observers;

use App\Models\TeacherSchedule;

class TeacherScheduleObserver
{
    public function saved(TeacherSchedule $sched)
    {
        // Schedule caches refresh
    }
}
