<?php
namespace App\Domain\Exam\Actions;

use App\DTOs\Exam\ExamResultDTO;
use App\Domain\Exam\Services\ExamResultService;
use App\Models\ExamResult;

class RecordExamResultsAction
{
    public function __construct(protected ExamResultService $service) {}

    public function execute(ExamResultDTO $dto): ExamResult
    {
        return $this->service->saveResult($dto);
    }
}
