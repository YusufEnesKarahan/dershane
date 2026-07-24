<?php

namespace App\Core\Repositories;

use App\Models\ParentStudent;
use App\Core\Repositories\Interfaces\ParentRepositoryInterface;
use Illuminate\Support\Collection;

class ParentRepository implements ParentRepositoryInterface
{
    public function getLinkedStudents(int $parentId): Collection
    {
        return ParentStudent::with(['student.classroom', 'student.enrollments.course'])
            ->where('parent_id', $parentId)
            ->get()
            ->pluck('student');
    }

    public function isStudentLinked(int $parentId, int $studentId): bool
    {
        return ParentStudent::query()
            ->where('parent_id', $parentId)
            ->where('student_id', $studentId)
            ->exists();
    }

    public function linkStudent(int $parentId, int $studentId, string $relationType): ParentStudent
    {
        return ParentStudent::updateOrCreate(
            [
                'parent_id' => $parentId,
                'student_id' => $studentId,
            ],
            [
                'relation_type' => $relationType,
            ]
        );
    }
}
