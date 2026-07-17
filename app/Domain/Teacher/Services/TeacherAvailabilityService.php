<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherAvailability;

class TeacherAvailabilityService
{
    public function saveAvailability(Teacher $teacher, array $availabilities): void
    {
        $teacher->availabilities()->delete();

        foreach ($availabilities as $av) {
            TeacherAvailability::create([
                'teacher_id' => $teacher->id,
                'day_of_week' => (int) $av['day_of_week'],
                'start_time' => $av['start_time'],
                'end_time' => $av['end_time'],
                'is_recurring' => $av['is_recurring'] ?? true,
            ]);
        }
    }
}
