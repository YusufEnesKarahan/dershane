<?php
namespace App\Core\Repositories;

use App\Models\TeacherPerformance;
use App\Core\Repositories\Interfaces\TeacherPerformanceRepositoryInterface;
use Illuminate\Support\Collection;

class TeacherPerformanceRepository implements TeacherPerformanceRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection
    {
        return TeacherPerformance::where('teacher_id', $teacherId)->orderBy('kpi_month', 'desc')->get();
    }

    public function create(array $data): TeacherPerformance
    {
        return TeacherPerformance::create($data);
    }
}
