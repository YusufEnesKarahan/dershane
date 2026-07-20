<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StudentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function all(): Collection;
    public function findById(int $id): ?Student;
    public function findByStudentNumber(string $number): ?Student;
    public function create(array $data): Student;
    public function update(Student $student, array $data): Student;
    public function delete(Student $student): bool;
}
