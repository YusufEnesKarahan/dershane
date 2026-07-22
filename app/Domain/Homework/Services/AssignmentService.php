<?php
namespace App\Domain\Homework\Services;

use App\DTOs\Homework\CreateAssignmentDTO;
use App\Core\Repositories\Interfaces\AssignmentRepositoryInterface;
use App\Models\Assignment;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    public function __construct(protected AssignmentRepositoryInterface $repository) {}

    public function createAssignment(CreateAssignmentDTO $dto): Assignment
    {
        $exists = $this->repository->findByCode($dto->code);
        if ($exists) {
            throw ValidationException::withMessages([
                'code' => 'Assignment code must be unique.',
            ]);
        }

        return $this->repository->create([
            'title' => $dto->title,
            'code' => $dto->code,
            'description' => $dto->description,
            'assignment_type' => $dto->assignment_type,
            'classroom_id' => $dto->classroom_id,
            'course_id' => $dto->course_id,
            'teacher_id' => $dto->teacher_id,
            'due_date' => $dto->due_date,
            'max_score' => $dto->max_score,
            'status' => $dto->status,
        ]);
    }
}
