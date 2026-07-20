<?php
namespace App\Core\Repositories;

use App\Models\Attendance;
use App\Core\Repositories\Interfaces\AttendanceRepositoryInterface;
use Illuminate\Support\Collection;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function getBySession(int $sessionId): Collection
    {
        return Attendance::with(['student', 'status'])->where('attendance_session_id', $sessionId)->get();
    }

    public function recordBulk(int $sessionId, array $records): void
    {
        foreach ($records as $record) {
            Attendance::updateOrCreate(
                [
                    'attendance_session_id' => $sessionId,
                    'student_id' => $record['student_id'],
                ],
                [
                    'attendance_status_id' => $record['attendance_status_id'],
                    'remarks' => $record['remarks'] ?? null,
                ]
            );
        }
    }
}
