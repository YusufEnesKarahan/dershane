<?php
namespace App\Core\Repositories;

use App\Models\Classroom;
use App\Core\Repositories\Interfaces\ClassroomRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Classroom::with(['branch', 'type'])->orderBy('code');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Classroom::with(['branch', 'type'])->get();
    }

    public function findById(int $id): ?Classroom
    {
        return Classroom::with(['branch', 'type', 'schedules.course', 'schedules.teacher.user'])->find($id);
    }

    public function findByCode(string $code): ?Classroom
    {
        return Classroom::where('code', $code)->first();
    }

    public function create(array $data): Classroom
    {
        return Classroom::create($data);
    }

    public function update(Classroom $classroom, array $data): Classroom
    {
        $classroom->update($data);
        return $classroom;
    }

    public function delete(Classroom $classroom): bool
    {
        return $classroom->delete();
    }
}
