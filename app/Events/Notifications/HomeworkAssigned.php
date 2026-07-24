<?php
namespace App\Events\Notifications;
use App\Models\Assignment;
class HomeworkAssigned { public function __construct(public readonly Assignment $assignment) {} }
