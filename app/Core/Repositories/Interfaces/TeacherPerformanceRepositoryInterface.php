<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\TeacherPerformance;
use Illuminate\Support\Collection;

interface TeacherPerformanceRepositoryInterface
{
    public function getByTeacher(int $teacherId): Collection;
    public function create(array $data): TeacherPerformance;
}
