<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Assignment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AssignmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?Assignment;
    public function findByCode(string $code): ?Assignment;
    public function create(array $data): Assignment;
}
