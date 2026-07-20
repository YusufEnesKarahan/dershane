<?php
namespace App\Domain\Course\Actions;

use App\DTOs\Course\UpdateCourseDTO;
use App\Domain\Course\Services\CourseService;
use App\Models\Course;

class UpdateCourseAction
{
    public function __construct(protected CourseService $service) {}

    public function execute(Course $course, UpdateCourseDTO $dto): Course
    {
        return $this->service->update($course, $dto);
    }
}
