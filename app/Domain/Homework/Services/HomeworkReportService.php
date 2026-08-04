<?php

namespace App\Domain\Homework\Services;

use App\Models\Homework;
use Illuminate\Support\Collection;

class HomeworkReportService
{
    public function getHomeworkStats(Homework $homework): array
    {
        $totalStudents = $homework->classroom ? $homework->classroom->students()->count() : 0;
        $submissions = $homework->submissions;
        
        $submittedCount = $submissions->count();
        $lateCount = $submissions->where('status', 'late')->count();
        $gradedSubmissions = $submissions->whereNotNull('grade');
        
        $averageGrade = $gradedSubmissions->avg('grade') ?? 0;
        
        return [
            'total_students' => $totalStudents,
            'submitted_count' => $submittedCount,
            'missing_count' => max(0, $totalStudents - $submittedCount),
            'late_count' => $lateCount,
            'average_grade' => round($averageGrade, 2),
            'submission_rate' => $totalStudents > 0 ? round(($submittedCount / $totalStudents) * 100, 2) : 0,
            'late_rate' => $submittedCount > 0 ? round(($lateCount / $submittedCount) * 100, 2) : 0,
        ];
    }
}
