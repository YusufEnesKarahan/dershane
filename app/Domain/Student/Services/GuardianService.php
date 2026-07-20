<?php
namespace App\Domain\Student\Services;

use App\DTOs\Student\GuardianDTO;
use App\Models\StudentGuardian;

class GuardianService
{
    public function addGuardian(GuardianDTO $dto): StudentGuardian
    {
        return StudentGuardian::create([
            'student_id' => $dto->student_id,
            'guardian_name' => $dto->guardian_name,
            'relation' => $dto->relation,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'is_primary' => $dto->is_primary,
        ]);
    }
}
