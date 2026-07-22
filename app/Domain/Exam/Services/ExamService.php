<?php
namespace App\Domain\Exam\Services;

use App\DTOs\Exam\CreateExamDTO;
use App\Core\Repositories\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;
use Illuminate\Validation\ValidationException;

class ExamService
{
    public function __construct(protected ExamRepositoryInterface $repository) {}

    public function createExam(CreateExamDTO $dto): Exam
    {
        $exists = $this->repository->findByCode($dto->code);
        if ($exists) {
            throw ValidationException::withMessages([
                'code' => 'Exam code must be unique.',
            ]);
        }

        return $this->repository->create([
            'title' => $dto->title,
            'code' => $dto->code,
            'exam_type' => $dto->exam_type,
            'exam_date' => $dto->exam_date,
            'total_questions' => $dto->total_questions,
            'duration_minutes' => $dto->duration_minutes,
            'is_published' => $dto->is_published,
        ]);
    }
}
