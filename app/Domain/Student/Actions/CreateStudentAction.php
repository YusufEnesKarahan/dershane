<?php
namespace App\Domain\Student\Actions;

use App\DTOs\Student\CreateStudentDTO;
use App\Domain\Student\Services\StudentService;
use App\Models\Student;

class CreateStudentAction
{
    public function __construct(protected StudentService $service) {}

    public function execute(CreateStudentDTO $dto): Student
    {
        return $this->service->create($dto);
    }
}
