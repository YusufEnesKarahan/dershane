<?php

namespace App\Core\Repositories;

use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Core\Repositories\Interfaces\TeacherPortalRepositoryInterface;
use Illuminate\Support\Collection;

class TeacherPortalRepository implements TeacherPortalRepositoryInterface
{
    public function findByUserId(int $userId): ?Teacher
    {
        return Teacher::with(['branch', 'user'])->where('user_id', $userId)->first();
    }

    public function getAssignedClasses(int $teacherId): Collection
    {
        return TeacherAssignment::with(['classroom', 'course'])
            ->where('teacher_id', $teacherId)
            ->get();
    }
}
