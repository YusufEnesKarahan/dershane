<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\Teacher;
use App\Models\TeacherAssignment;
use Illuminate\Support\Collection;

interface TeacherPortalRepositoryInterface
{
    public function findByUserId(int $userId): ?Teacher;
    public function getAssignedClasses(int $teacherId): Collection;

    public function hasAssignment(int $teacherId, int $classroomId, int $courseId): bool;
}
