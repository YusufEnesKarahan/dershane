<?php
namespace App\Observers;

use App\Models\Exam;
use Illuminate\Support\Facades\Cache;

class ExamObserver
{
    public function saved(Exam $exam)
    {
        Cache::forget('exams.analytics.summary');
    }
}
