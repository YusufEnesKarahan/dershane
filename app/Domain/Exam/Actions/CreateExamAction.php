<?php
namespace App\Domain\Exam\Actions;

use App\DTOs\Exam\CreateExamDTO;
use App\Domain\Exam\Services\ExamService;
use App\Models\Exam;

class CreateExamAction
{
    public function __construct(protected ExamService $service) {}

    public function execute(CreateExamDTO $dto): Exam
    {
        return $this->service->createExam($dto);
    }
}
