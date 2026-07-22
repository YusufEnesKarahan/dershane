<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\ExamResult;
use Illuminate\Support\Collection;

interface ExamResultRepositoryInterface
{
    public function getByExam(int $examId): Collection;
    public function createOrUpdate(array $data): ExamResult;
}
