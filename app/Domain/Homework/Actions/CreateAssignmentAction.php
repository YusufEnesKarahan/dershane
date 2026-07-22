<?php
namespace App\Domain\Homework\Actions;

use App\DTOs\Homework\CreateAssignmentDTO;
use App\Domain\Homework\Services\AssignmentService;
use App\Models\Assignment;

class CreateAssignmentAction
{
    public function __construct(protected AssignmentService $service) {}

    public function execute(CreateAssignmentDTO $dto): Assignment
    {
        return $this->service->createAssignment($dto);
    }
}
