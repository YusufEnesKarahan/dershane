<?php
namespace App\Domain\Teacher\Actions;

use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Domain\Teacher\Services\TeacherService;
use App\Models\Teacher;

class UpdateTeacherAction
{
    public function __construct(protected TeacherService $service) {}

    public function execute(Teacher $teacher, UpdateTeacherDTO $dto): Teacher
    {
        return $this->service->update($teacher, $dto);
    }
}
