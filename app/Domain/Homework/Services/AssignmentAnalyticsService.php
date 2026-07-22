<?php
namespace App\Domain\Homework\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentScore;

class AssignmentAnalyticsService
{
    public function getSummary(): array
    {
        $totalAssignments = Assignment::count();
        $totalSubmissions = AssignmentSubmission::count();
        $lateSubmissions = AssignmentSubmission::where('is_late', true)->count();
        $avgScore = AssignmentScore::avg('score');

        $submissionRate = $totalAssignments > 0 ? round(($totalSubmissions / max(1, $totalAssignments * 20)) * 100, 1) : 0;
        $lateRate = $totalSubmissions > 0 ? round(($lateSubmissions / $totalSubmissions) * 100, 1) : 0;

        return [
            'total_assignments' => $totalAssignments,
            'total_submissions' => $totalSubmissions,
            'late_submissions' => $lateSubmissions,
            'submission_rate' => $submissionRate,
            'late_rate' => $lateRate,
            'avg_score' => round($avgScore ?? 0, 1),
        ];
    }
}
