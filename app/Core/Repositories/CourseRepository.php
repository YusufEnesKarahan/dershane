<?php
namespace App\Core\Repositories;

use App\Models\Course;
use App\Core\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository implements CourseRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Course::with(['level', 'currentPricing'])->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['level_id'])) {
            $query->where('course_level_id', $filters['level_id']);
        }

        return $query->paginate($perPage);
    }

    public function all(): Collection
    {
        return Course::with(['currentPricing'])->get();
    }

    public function findById(int $id): ?Course
    {
        return Course::with(['level', 'subjects', 'modules', 'materials', 'pricings', 'prerequisites', 'teachers.user', 'branches'])->find($id);
    }

    public function findByCode(string $code): ?Course
    {
        return Course::where('code', $code)->first();
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function delete(Course $course): bool
    {
        return $course->delete();
    }
}
