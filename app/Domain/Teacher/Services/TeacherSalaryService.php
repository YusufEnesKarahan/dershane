<?php
namespace App\Domain\Teacher\Services;

use App\Models\Teacher;
use App\Models\TeacherSalaryProfile;

class TeacherSalaryService
{
    public function saveSalary(Teacher $teacher, array $data): TeacherSalaryProfile
    {
        return TeacherSalaryProfile::updateOrCreate(
            ['teacher_id' => $teacher->id],
            [
                'base_salary' => $data['base_salary'] ?? 0.0,
                'payment_type' => $data['payment_type'] ?? 'Monthly',
                'bonus' => $data['bonus'] ?? 0.0,
                'deductions' => $data['deductions'] ?? 0.0,
            ]
        );
    }
}
