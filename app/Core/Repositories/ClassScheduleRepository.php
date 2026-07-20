<?php
namespace App\Core\Repositories;

use App\Models\ClassSchedule;
use App\Core\Repositories\Interfaces\ClassScheduleRepositoryInterface;
use Illuminate\Support\Collection;

class ClassScheduleRepository implements ClassScheduleRepositoryInterface
{
    public function getByClassroom(int $classroomId): Collection
    {
        return ClassSchedule::with(['teacher.user', 'course', 'classroom'])->where('classroom_id', $classroomId)->get();
    }

    public function getByTeacher(int $teacherId): Collection
    {
        return ClassSchedule::with(['classroom', 'course'])->where('teacher_id', $teacherId)->get();
    }

    public function create(array $data): ClassSchedule
    {
        return ClassSchedule::create($data);
    }
}
