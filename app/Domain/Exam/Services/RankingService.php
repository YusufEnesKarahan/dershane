<?php
namespace App\Domain\Exam\Services;

use App\Models\ExamResult;
use App\Models\ExamRanking;
use App\Models\Student;

class RankingService
{
    public function updateRankings(int $examId): void
    {
        $results = ExamResult::with('student')
            ->where('exam_id', $examId)
            ->where('is_absent', false)
            ->orderBy('score', 'desc')
            ->get();

        $globalRank = 1;
        $branchRanks = [];

        foreach ($results as $res) {
            $branchId = $res->student->branch_id ?? 1;
            if (!isset($branchRanks[$branchId])) {
                $branchRanks[$branchId] = 1;
            }

            $currentBranchRank = $branchRanks[$branchId]++;
            $res->update([
                'branch_rank' => $currentBranchRank,
                'global_rank' => $globalRank,
            ]);

            ExamRanking::updateOrCreate(
                [
                    'exam_id' => $examId,
                    'student_id' => $res->student_id,
                ],
                [
                    'branch_id' => $branchId,
                    'score' => $res->score,
                    'branch_rank' => $currentBranchRank,
                    'global_rank' => $globalRank,
                ]
            );

            $globalRank++;
        }
    }
}
