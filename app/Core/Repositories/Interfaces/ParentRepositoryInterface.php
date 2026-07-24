<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\ParentStudent;
use App\Models\Student;
use Illuminate\Support\Collection;

interface ParentRepositoryInterface
{
    public function getLinkedStudents(int $parentId): Collection;

    public function isStudentLinked(int $parentId, int $studentId): bool;

    public function linkStudent(int $parentId, int $studentId, string $relationType): ParentStudent;
}
