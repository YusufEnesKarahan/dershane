<?php
namespace App\Observers;

use App\Models\Classroom;
use Illuminate\Support\Facades\Cache;

class ClassroomObserver
{
    public function saved(Classroom $classroom)
    {
        Cache::forget('classrooms.analytics.summary');
    }
}
