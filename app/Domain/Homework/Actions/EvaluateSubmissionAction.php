<?php
namespace App\Domain\Homework\Actions;

use App\DTOs\Homework\EvaluateSubmissionDTO;
use App\Domain\Homework\Services\AssignmentEvaluationService;
use App\Models\AssignmentScore;

class EvaluateSubmissionAction
{
    public function __construct(protected AssignmentEvaluationService $service) {}

    public function execute(EvaluateSubmissionDTO $dto): AssignmentScore
    {
        return $this->service->evaluate($dto);
    }
}
