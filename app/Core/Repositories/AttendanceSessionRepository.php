<?php
namespace App\Core\Repositories;

use App\Models\AttendanceSession;
use App\Core\Repositories\Interfaces\AttendanceSessionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceSessionRepository implements AttendanceSessionRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = AttendanceSession::with(['classroom', 'course', 'teacher.user'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc');

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?AttendanceSession
    {
        return AttendanceSession::with(['classroom', 'course', 'teacher.user', 'attendances.student', 'attendances.status'])->find($id);
    }

    public function create(array $data): AttendanceSession
    {
        return AttendanceSession::create($data);
    }
}
