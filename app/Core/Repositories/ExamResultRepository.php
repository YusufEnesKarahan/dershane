<?php
namespace App\Core\Repositories;

use App\Models\ExamResult;
use App\Core\Repositories\Interfaces\ExamResultRepositoryInterface;
use Illuminate\Support\Collection;

class ExamResultRepository implements ExamResultRepositoryInterface
{
    public function getByExam(int $examId): Collection
    {
        return ExamResult::with(['student.branch', 'subjectResults'])
            ->where('exam_id', $examId)
            ->orderBy('total_net', 'desc')
            ->get();
    }

    public function createOrUpdate(array $data): ExamResult
    {
        return ExamResult::updateOrCreate(
            [
                'exam_id' => $data['exam_id'],
                'student_id' => $data['student_id'],
            ],
            $data
        );
    }
}
