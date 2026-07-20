<?php
namespace App\Domain\Course\Actions;

use App\Models\Course;

class RestoreCourseAction
{
    public function execute(Course $course): void
    {
        $course->restore();
    }
}
