<?php

namespace App\Domain\Teacher\Services;

use App\Models\TeacherSchedule;
use Illuminate\Support\Collection;

class TeacherScheduleService
{
    public function getSchedules(int $teacherId): Collection
    {
        return TeacherSchedule::with(['classroom', 'course'])
            ->where('teacher_id', $teacherId)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
    }
}
