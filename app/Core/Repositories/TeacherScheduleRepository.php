<?php
namespace App\Core\Repositories;

use App\Models\TeacherSchedule;
use App\Core\Repositories\Interfaces\TeacherScheduleRepositoryInterface;
use Illuminate\Support\Collection;

class TeacherScheduleRepository implements TeacherScheduleRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection
    {
        return TeacherSchedule::where('teacher_id', $teacherId)->with(['classroom', 'course'])->get();
    }

    public function checkOverlap(int $teacherId, string $date, string $startTime, string $endTime): bool
    {
        return TeacherSchedule::where('teacher_id', $teacherId)
            ->where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                          ->where('end_time', '>=', $endTime);
                    });
            })->exists();
    }

    public function create(array $data): TeacherSchedule
    {
        return TeacherSchedule::create($data);
    }
}
