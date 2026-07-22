<?php

namespace App\Observers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Cache;

class TeacherObserver
{
    public function saved(Teacher $teacher)
    {
        Cache::forget('teachers.analytics.summary');
    }
}
