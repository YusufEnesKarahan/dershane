<?php
namespace App\Events\System;
class StudentAbsenceDetectedEvent { public function __construct(public readonly int $studentId, public readonly string $date) {} }
