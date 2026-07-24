<?php
namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Facades\Cache;

class StudentObserver
{
    public function saved(Student $student)
    {
        Cache::forget('students.analytics.summary');
        if ($student->wasRecentlyCreated) event(new \App\Events\Notifications\StudentRegistered($student));
    }
}
