<?php
namespace App\Domain\Course\Actions;

use App\Models\Course;

class DeleteCourseAction
{
    public function execute(Course $course): void
    {
        $course->delete();
    }
}
