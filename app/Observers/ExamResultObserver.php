<?php
namespace App\Observers;
use App\Models\ExamResult;
class ExamResultObserver { public function created(ExamResult $result): void { event(new \App\Events\Notifications\ExamResultPublished($result)); } }
