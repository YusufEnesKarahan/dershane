<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Exam;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ExamRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Exam;
    public function findByCode(string $code): ?Exam;
    public function create(array $data): Exam;
}
