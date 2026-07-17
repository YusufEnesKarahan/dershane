<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\TeacherSchedule;
use Illuminate\Support\Collection;

interface TeacherScheduleRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection;
    public function checkOverlap(int $teacherId, string $date, string $startTime, string $endTime): bool;
    public function create(array $data): TeacherSchedule;
}
