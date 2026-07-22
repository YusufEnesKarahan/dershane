<?php

namespace App\Domain\Teacher\Actions;

use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Domain\Teacher\Services\TeacherService;
use App\Models\Teacher;

class UpdateTeacherProfile
{
    public function __construct(protected TeacherService $service) {}

    public function execute(int $id, UpdateTeacherDTO $dto): Teacher
    {
        return $this->service->updateTeacher($id, $dto);
    }
}
