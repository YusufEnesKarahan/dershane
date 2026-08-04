<?php

namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamAnswer;

class ExamAnalysisService
{
    public function getExamAnalysis(Exam $exam): array
    {
        $results = ExamResult::with('student', 'answers.course')->where('exam_id', $exam->id)->get();
        
        if ($results->isEmpty()) {
            return [
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'participation_count' => 0,
                'top_students' => [],
                'low_performing_students' => [],
                'course_analysis' => []
            ];
        }
        
        $averageScore = $results->avg('score');
        $highestScore = $results->max('score');
        $lowestScore = $results->min('score');
        
        $topStudents = $results->sortByDesc('score')->take(5)->values();
        $lowPerformingStudents = $results->sortBy('score')->take(5)->values();
        
        // Course Analysis
        $courseAnalysis = [];
        foreach ($results as $result) {
            foreach ($result->answers as $answer) {
                $courseId = $answer->course_id;
                if (!isset($courseAnalysis[$courseId])) {
                    $courseAnalysis[$courseId] = [
                        'course_name' => $answer->course->name,
                        'total_correct' => 0,
                        'total_wrong' => 0,
                        'total_empty' => 0,
                        'total_students' => 0,
                    ];
                }
                
                $courseAnalysis[$courseId]['total_correct'] += $answer->correct;
                $courseAnalysis[$courseId]['total_wrong'] += $answer->wrong;
                $courseAnalysis[$courseId]['total_empty'] += $answer->empty;
                $courseAnalysis[$courseId]['total_students']++;
            }
        }
        
        // Calculate averages per course
        foreach ($courseAnalysis as &$analysis) {
            $total = $analysis['total_students'];
            if ($total > 0) {
                $analysis['avg_correct'] = round($analysis['total_correct'] / $total, 2);
                $analysis['avg_wrong'] = round($analysis['total_wrong'] / $total, 2);
                $analysis['avg_empty'] = round($analysis['total_empty'] / $total, 2);
            }
        }
        
        return [
            'average_score' => round($averageScore, 2),
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'participation_count' => $results->count(),
            'top_students' => $topStudents,
            'low_performing_students' => $lowPerformingStudents,
            'course_analysis' => array_values($courseAnalysis)
        ];
    }
}
