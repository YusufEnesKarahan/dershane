<?php

namespace App\Domain\Finance\Services;

use App\Models\Scholarship;

class ScholarshipService
{
    public function assignScholarship(int $studentId, string $title, float $percentage): Scholarship
    {
        return Scholarship::create([
            'student_id' => $studentId,
            'title' => $title,
            'percentage' => $percentage,
            'is_active' => true,
        ]);
    }
}
