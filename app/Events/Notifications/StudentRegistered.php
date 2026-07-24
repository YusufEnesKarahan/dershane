<?php
namespace App\Events\Notifications;
use App\Models\Student;
class StudentRegistered { public function __construct(public readonly Student $student) {} }
