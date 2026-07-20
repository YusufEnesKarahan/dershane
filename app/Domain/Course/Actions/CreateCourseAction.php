<?php
namespace App\Domain\Course\Actions;

use App\DTOs\Course\CreateCourseDTO;
use App\Domain\Course\Services\CourseService;
use App\Models\Course;

class CreateCourseAction
{
    public function __construct(protected CourseService $service) {}

    public function execute(CreateCourseDTO $dto): Course
    {
        return $this->service->create($dto);
    }
}
