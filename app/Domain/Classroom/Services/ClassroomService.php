<?php
namespace App\Domain\Classroom\Services;

use App\DTOs\Classroom\CreateClassroomDTO;
use App\DTOs\Classroom\UpdateClassroomDTO;
use App\Core\Repositories\Interfaces\ClassroomRepositoryInterface;
use App\Models\Classroom;
use Illuminate\Validation\ValidationException;

class ClassroomService
{
    public function __construct(protected ClassroomRepositoryInterface $repository) {}

    public function create(CreateClassroomDTO $dto): Classroom
    {
        $exists = $this->repository->findByCode($dto->code);
        if ($exists) {
            throw ValidationException::withMessages([
                'code' => 'Classroom code must be unique.',
            ]);
        }

        return $this->repository->create([
            'code' => $dto->code,
            'name' => $dto->name,
            'branch_id' => $dto->branch_id,
            'classroom_type_id' => $dto->classroom_type_id,
            'capacity' => $dto->capacity,
            'color_code' => $dto->color_code,
            'is_active' => $dto->is_active,
        ]);
    }

    public function update(Classroom $classroom, UpdateClassroomDTO $dto): Classroom
    {
        return $this->repository->update($classroom, [
            'name' => $dto->name,
            'branch_id' => $dto->branch_id,
            'classroom_type_id' => $dto->classroom_type_id,
            'capacity' => $dto->capacity,
            'color_code' => $dto->color_code,
            'is_active' => $dto->is_active,
        ]);
    }
}
