<?php

namespace App\Domain\Exam\Services;

use App\Models\ExamResult;
use App\Models\Student;

class ParentExamService
{
    public function getChildResults(Student $student)
    {
        return ExamResult::with('exam.type')
            ->where('student_id', $student->id)
            ->where('branch_id', $student->branch_id)
            ->orderByDesc('created_at')
            ->get();
    }
}
