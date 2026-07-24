<?php
namespace App\Events\Notifications;
use App\Models\ExamResult;
class ExamResultPublished { public function __construct(public readonly ExamResult $result) {} }
