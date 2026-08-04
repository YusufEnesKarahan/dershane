<?php

namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use App\Models\ExamRanking;
use Illuminate\Support\Facades\DB;

class ExamResultService
{
    public function submitResult(Exam $exam, array $data): ExamResult
    {
        return DB::transaction(function () use ($exam, $data) {
            $result = ExamResult::updateOrCreate(
                [
                    'branch_id' => $exam->branch_id,
                    'exam_id' => $exam->id,
                    'student_id' => $data['student_id']
                ],
                [
                    'score' => $data['score'] ?? 0,
                    'correct_answers' => $data['correct_answers'] ?? 0,
                    'wrong_answers' => $data['wrong_answers'] ?? 0,
                    'empty_answers' => $data['empty_answers'] ?? 0,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            if (isset($data['answers']) && is_array($data['answers'])) {
                foreach ($data['answers'] as $courseId => $answerData) {
                    ExamAnswer::updateOrCreate(
                        [
                            'branch_id' => $exam->branch_id,
                            'exam_result_id' => $result->id,
                            'course_id' => $courseId,
                        ],
                        [
                            'correct' => $answerData['correct'] ?? 0,
                            'wrong' => $answerData['wrong'] ?? 0,
                            'empty' => $answerData['empty'] ?? 0,
                        ]
                    );
                }
            }
            
            $this->calculateRankings($exam);
            
            return $result;
        });
    }

    public function calculateRankings(Exam $exam): void
    {
        $results = ExamResult::where('exam_id', $exam->id)
            ->orderByDesc('score')
            ->get();
            
        $rank = 1;
        $totalStudents = $results->count();
        
        foreach ($results as $result) {
            $percentile = $totalStudents > 1 ? (($totalStudents - $rank) / ($totalStudents - 1)) * 100 : 100;
            
            $result->update([
                'rank' => $rank,
                'percentile' => round($percentile, 2)
            ]);
            
            ExamRanking::updateOrCreate(
                [
                    'branch_id' => $exam->branch_id,
                    'exam_id' => $exam->id,
                    'student_id' => $result->student_id
                ],
                [
                    'score' => $result->score,
                    'rank' => $rank
                ]
            );
            
            $rank++;
        }
    }
}
