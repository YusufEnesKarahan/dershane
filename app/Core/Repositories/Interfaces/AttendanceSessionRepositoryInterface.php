<?php
namespace App\Core\Repositories\Interfaces;

use App\Models\AttendanceSession;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AttendanceSessionRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findById(int $id): ?AttendanceSession;
    public function create(array $data): AttendanceSession;
}
