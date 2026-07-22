<?php
namespace App\Observers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Cache;

class AssignmentObserver
{
    public function saved(Assignment $assignment)
    {
        Cache::forget('homework.analytics.summary');
    }
}
