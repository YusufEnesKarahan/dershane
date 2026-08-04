<?php

namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Support\Facades\DB;

class ExamManagementService
{
    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            return Exam::create($data);
        });
    }

    public function canCreateExam(int $branchId, $plan): bool
    {
        $limit = $plan->limits['max_exams'] ?? null;

        // Unlimited exams
        if ($limit === null || $limit === 0) {
            return true;
        }

        $currentExamCount = \App\Models\Exam::where('branch_id', $branchId)->count();
        
        return $currentExamCount < $limit;
    }

    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            $exam->update($data);
            return $exam;
        });
    }

    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            return $exam->delete();
        });
    }

    public function assignClassroom(Exam $exam, int $classroomId): Exam
    {
        return DB::transaction(function () use ($exam, $classroomId) {
            $exam->update(['classroom_id' => $classroomId]);
            return $exam;
        });
    }

    public function enterResults(Exam $exam, array $resultsData): void
    {
        DB::transaction(function () use ($exam, $resultsData) {
            foreach ($resultsData as $result) {
                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $result['student_id']
                    ],
                    [
                        'branch_id' => $exam->branch_id,
                        'score' => $result['score'],
                        'notes' => $result['notes'] ?? null,
                    ]
                );
            }
            $this->calculateRanking($exam);
        });
    }

    public function updateResult(ExamResult $result, array $data): ExamResult
    {
        return DB::transaction(function () use ($result, $data) {
            $result->update($data);
            $this->calculateRanking($result->exam);
            return $result;
        });
    }

    public function calculateRanking(Exam $exam): void
    {
        DB::transaction(function () use ($exam) {
            $results = $exam->results()->orderByDesc('score')->get();
            $rank = 1;
            foreach ($results as $result) {
                $result->update(['rank' => $rank]);
                $rank++;
            }
        });
    }

    public function getExamStatistics(Exam $exam): array
    {
        $results = $exam->results();
        $count = $results->count();

        if ($count === 0) {
            return [
                'total_students' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
            ];
        }

        return [
            'total_students' => $count,
            'average_score' => $results->avg('score'),
            'highest_score' => $results->max('score'),
            'lowest_score' => $results->min('score'),
        ];
    }
}
