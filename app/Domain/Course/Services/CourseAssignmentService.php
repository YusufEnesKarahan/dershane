<?php
namespace App\Domain\Course\Services;

use App\Models\Course;

class CourseAssignmentService
{
    public function assignTeachers(Course $course, array $teacherIds): void
    {
        $course->teachers()->sync($teacherIds);
    }

    public function assignBranches(Course $course, array $branchIds): void
    {
        $course->branches()->sync($branchIds);
    }
}
