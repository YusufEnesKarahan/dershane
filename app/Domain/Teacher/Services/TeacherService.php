<?php

namespace App\Domain\Teacher\Services;

use App\DTOs\Teacher\CreateTeacherDTO;
use App\DTOs\Teacher\UpdateTeacherDTO;
use App\Models\Teacher;

class TeacherService
{
    public function createTeacher(CreateTeacherDTO $dto): Teacher
    {
        return Teacher::create([
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
