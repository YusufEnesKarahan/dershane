<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\ClassSchedule;
use Illuminate\Support\Collection;

interface ClassScheduleRepositoryInterface
{
    public function getByClassroom(int $classroomId): Collection;
    public function getByTeacher(int $teacherId): Collection;
    public function create(array $data): ClassSchedule;
}
