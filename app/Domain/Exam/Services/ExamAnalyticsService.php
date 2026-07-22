<?php
namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\ExamResult;

class ExamAnalyticsService
{
    public function getSummary(): array
    {
        $totalExams = Exam::count();
        $totalResults = ExamResult::count();
        $avgNet = ExamResult::where('is_absent', false)->avg('total_net');
        $topScore = ExamResult::max('score');

        return [
            'total_exams' => $totalExams,
            'total_results' => $totalResults,
            'avg_net' => round($avgNet ?? 0, 2),
            'top_score' => round($topScore ?? 0, 2),
        ];
    }
}
