<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;

class TeacherProfileService
{
    public function updateProfile(Teacher $teacher, array $data): void
    {
        $teacher->update($data);
    }
}
