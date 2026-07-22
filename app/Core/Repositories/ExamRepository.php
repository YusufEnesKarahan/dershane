<?php
namespace App\Core\Repositories;

use App\Models\Exam;
use App\Core\Repositories\Interfaces\ExamRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ExamRepository implements ExamRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Exam::withCount('results')->orderBy('exam_date', 'desc');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Exam
    {
        return Exam::with(['results.student.branch', 'rankings.student.branch'])->find($id);
    }

    public function findByCode(string $code): ?Exam
    {
        return Exam::where('code', $code)->first();
    }

    public function create(array $data): Exam
    {
        return Exam::create($data);
    }
}
