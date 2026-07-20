<?php
namespace App\Domain\Course\Services;

use App\Models\Course;

class CourseAnalyticsService
{
    public function getAnalyticsSummary(): array
    {
        return [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_capacity' => Course::sum('capacity'),
        ];
    }
}
