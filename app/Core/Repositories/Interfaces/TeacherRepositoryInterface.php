<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Teacher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TeacherRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function all(): Collection;
    public function findById(int $id): ?Teacher;
    public function create(array $data): Teacher;
    public function update(Teacher $teacher, array $data): Teacher;
    public function delete(Teacher $teacher): bool;
}
