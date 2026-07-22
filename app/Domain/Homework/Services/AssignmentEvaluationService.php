<?php
namespace App\Domain\Homework\Services;

use App\DTOs\Homework\EvaluateSubmissionDTO;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentScore;

class AssignmentEvaluationService
{
    public function evaluate(EvaluateSubmissionDTO $dto): AssignmentScore
    {
        $submission = AssignmentSubmission::findOrFail($dto->submission_id);

        $scoreRecord = AssignmentScore::updateOrCreate(
            [
                'submission_id' => $dto->submission_id,
            ],
            [
                'evaluator_id' => $dto->evaluator_id,
                'score' => $dto->score,
                'max_score' => $dto->max_score,
                'feedback' => $dto->feedback,
            ]
        );

        $submission->update([
            'status' => 'Graded',
        ]);

        return $scoreRecord;
    }
}
