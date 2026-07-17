<?php
namespace App\Core\Repositories;

use App\Models\Teacher;
use App\Core\Repositories\Interfaces\TeacherRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TeacherRepository implements TeacherRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Teacher::with(['user', 'branch'])->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Teacher::with('user')->get();
    }

    public function findById(int $id): ?Teacher
    {
        return Teacher::with([
            'user', 'branch', 'documents', 'certificates', 'experiences', 
            'availabilities', 'schedules.classroom', 'schedules.course', 
            'notes', 'performances', 'leaveRequests', 'salaryProfile', 'contracts'
        ])->find($id);
    }

    public function create(array $data): Teacher
    {
        return Teacher::create($data);
    }

    public function update(Teacher $teacher, array $data): Teacher
    {
        $teacher->update($data);
        return $teacher;
    }

    public function delete(Teacher $teacher): bool
    {
        return $teacher->delete();
    }
}
