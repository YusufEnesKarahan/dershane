<?php
namespace App\Domain\Teacher\Services;

use App\DTOs\Teacher\CreateTeacherDTO;
use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Core\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Models\Teacher;

class TeacherService
{
    public function __construct(protected TeacherRepositoryInterface $repository) {}

    public function create(CreateTeacherDTO $dto): Teacher
    {
        return $this->repository->create([
            'user_id' => $dto->user_id,
            'branch_id' => $dto->branch_id,
            'title' => $dto->title,
            'bio' => $dto->bio,
            'specialties' => $dto->specialties,
            'education' => $dto->education,
            'experience_years' => $dto->experience_years,
            'emergency_contact' => $dto->emergency_contact,
            'status' => $dto->status,
        ]);
    }

    public function update(Teacher $teacher, UpdateTeacherDTO $dto): Teacher
    {
        return $this->repository->update($teacher, [
            'branch_id' => $dto->branch_id,
            'title' => $dto->title,
            'bio' => $dto->bio,
            'specialties' => $dto->specialties,
            'education' => $dto->education,
            'experience_years' => $dto->experience_years,
            'emergency_contact' => $dto->emergency_contact,
            'status' => $dto->status,
        ]);
    }
}
