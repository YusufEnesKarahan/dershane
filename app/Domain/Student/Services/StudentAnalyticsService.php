<?php
namespace App\Domain\Student\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;

class StudentAnalyticsService
{
    public function getSummary(): array
    {
        return [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'Active')->count(),
            'graduated_students' => Student::where('status', 'Graduated')->count(),
            'total_enrollments' => StudentEnrollment::count(),
        ];
    }
}
