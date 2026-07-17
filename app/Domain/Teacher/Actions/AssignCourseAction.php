<?php
namespace App\Domain\Teacher\Actions;

use App\Models\Teacher;

class AssignCourseAction
{
    public function execute(Teacher $teacher, int $courseId): void
    {
        // Many-to-many courses assignment stub
    }
}
