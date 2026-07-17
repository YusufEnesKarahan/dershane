<?php
namespace App\Domain\Teacher\Actions;

use App\DTOs\Teacher\CreateTeacherDTO;
use App\Domain\Teacher\Services\TeacherService;
use App\Models\Teacher;

class CreateTeacherAction
{
    public function __construct(protected TeacherService $service) {}

    public function execute(CreateTeacherDTO $dto): Teacher
    {
        return $this->service->create($dto);
    }
}
