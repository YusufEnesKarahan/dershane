<?php
namespace App\Core\Repositories;

use App\Models\Assignment;
use App\Core\Repositories\Interfaces\AssignmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Assignment::with(['classroom', 'course', 'teacher.user'])
            ->withCount('submissions')
            ->orderBy('due_date', 'desc');

        if (!empty($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Assignment
    {
        return Assignment::with(['classroom', 'course', 'teacher.user', 'submissions.student', 'submissions.score'])->find($id);
    }

    public function findByCode(string $code): ?Assignment
    {
        return Assignment::where('code', $code)->first();
    }

    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }
}
