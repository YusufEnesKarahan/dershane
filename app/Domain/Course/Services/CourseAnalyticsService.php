<?php
namespace App\Domain\Course\Services;

use App\Models\Course;

class CourseAnalyticsService
{
    public function getAnalyticsSummary(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('courses.analytics.summary', 600, function () {
            return [
                'total_courses' => Course::count(),
                'active_courses' => Course::where('is_active', true)->count(),
                'total_capacity' => Course::sum('capacity'),
            ];
        });
    }
}
