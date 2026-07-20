<?php
namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class CourseObserver
{
    public function saved(Course $course)
    {
        Cache::forget('courses.analytics.summary');
    }
}
