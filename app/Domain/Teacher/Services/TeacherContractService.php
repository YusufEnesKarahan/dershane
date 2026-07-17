<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherContract;

class TeacherContractService
{
    public function signContract(Teacher $teacher, array $data): TeacherContract
    {
        return TeacherContract::create([
            'teacher_id' => $teacher->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'employment_type' => $data['employment_type'] ?? 'Full-time',
            'status' => 'Active',
        ]);
    }
}
