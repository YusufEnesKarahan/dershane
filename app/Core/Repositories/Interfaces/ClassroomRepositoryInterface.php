<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Classroom;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ClassroomRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function all(): Collection;
    public function findById(int $id): ?Classroom;
    public function findByCode(string $code): ?Classroom;
    public function create(array $data): Classroom;
    public function update(Classroom $classroom, array $data): Classroom;
    public function delete(Classroom $classroom): bool;
}
