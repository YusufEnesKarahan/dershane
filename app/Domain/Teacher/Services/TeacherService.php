<?php

namespace App\Domain\Teacher\Services;

use App\Domain\Platform\Services\SubscriptionLimitService;
use App\DTOs\Teacher\CreateTeacherDTO;
use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Models\Branch;
use App\Models\Teacher;
use Illuminate\Auth\Access\AuthorizationException;

class TeacherService
{
    public function __construct(protected SubscriptionLimitService $limitService) {}

    public function createTeacher(CreateTeacherDTO $dto): Teacher
    {
        $branch = $dto->branch_id ? Branch::query()->findOrFail($dto->branch_id) : $this->limitService->resolveBranch();

        if ($branch && !$this->limitService->canAddTeacher($branch)) {
            throw new AuthorizationException('Mevcut abonelik planı öğretmen eklemeye izin vermiyor.');
        }

        return Teacher::create([
            'user_id' => $dto->user_id,
            'branch_id' => $branch?->id,
            'title' => $dto->title,
            'bio' => $dto->bio,
            'specialties' => $dto->specialties,
            'education' => $dto->education,
            'experience_years' => $dto->experience_years,
            'emergency_contact' => $dto->emergency_contact,
            'status' => $dto->status,
        ]);
    }

    public function updateTeacher(int $id, UpdateTeacherDTO $dto): Teacher
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update([
            'title' => $dto->title,
            'bio' => $dto->bio,
            'specialties' => $dto->specialties,
            'education' => $dto->education,
            'experience_years' => $dto->experience_years,
            'emergency_contact' => $dto->emergency_contact,
            'status' => $dto->status,
        ]);
        return $teacher;
    }
}
