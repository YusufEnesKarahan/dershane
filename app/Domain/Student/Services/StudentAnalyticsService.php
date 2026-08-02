<?php
namespace App\Domain\Student\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;

/**
 * Service for handling student-related analytics and metrics.
 */
class StudentAnalyticsService
{
    private const CACHE_KEY = 'students.analytics.summary';
    private const CACHE_TTL = 600;

    /**
     * Get a summary of student metrics, cached for performance.
     *
     * @return array<string, int>
     */
    public function getSummary(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return [
                'total_students' => Student::count(),
                'active_students' => Student::where('status', 'Active')->count(),
                'graduated_students' => Student::where('status', 'Graduated')->count(),
                'total_enrollments' => StudentEnrollment::count(),
            ];
        });
    }
}
