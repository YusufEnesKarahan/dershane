<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function all(): Collection;
    public function findById(int $id): ?Course;
    public function findByCode(string $code): ?Course;
    public function create(array $data): Course;
    public function update(Course $course, array $data): Course;
    public function delete(Course $course): bool;
}
