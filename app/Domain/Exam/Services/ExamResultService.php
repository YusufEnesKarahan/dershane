<?php
namespace App\Domain\Exam\Services;

use App\DTOs\Exam\ExamResultDTO;
use App\Models\ExamResult;
use App\Models\ExamSubjectResult;

class ExamResultService
{
    public function __construct(
        protected ScoreCalculationService $scoreService,
        protected RankingService $rankingService
    ) {}

    public function saveResult(ExamResultDTO $dto): ExamResult
    {
        $net = $this->scoreService->calculateNet($dto->total_correct, $dto->total_wrong);
        $score = $this->scoreService->calculateScore($net);

        $result = ExamResult::updateOrCreate(
            [
                'exam_id' => $dto->exam_id,
                'student_id' => $dto->student_id,
            ],
            [
                'total_correct' => $dto->total_correct,
                'total_wrong' => $dto->total_wrong,
                'total_empty' => $dto->total_empty,
                'total_net' => $net,
                'score' => $score,
                'is_absent' => $dto->is_absent,
            ]
        );

        if (!empty($dto->subject_breakdown)) {
            foreach ($dto->subject_breakdown as $sb) {
                $subNet = $this->scoreService->calculateNet($sb['correct'], $sb['wrong']);
                ExamSubjectResult::updateOrCreate(
                    [
                        'exam_result_id' => $result->id,
                        'subject_name' => $sb['subject_name'],
                    ],
                    [
                        'correct_count' => $sb['correct'],
                        'wrong_count' => $sb['wrong'],
                        'empty_count' => $sb['empty'],
                        'net_count' => $subNet,
                    ]
                );
            }
        }

        // Recalculate standings
        $this->rankingService->updateRankings($dto->exam_id);

        return $result;
    }
}
