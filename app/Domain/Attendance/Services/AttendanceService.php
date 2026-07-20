<?php
namespace App\Domain\Attendance\Services;

use App\DTOs\Attendance\BulkAttendanceDTO;
use App\Core\Repositories\Interfaces\AttendanceRepositoryInterface;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(protected AttendanceRepositoryInterface $repository) {}

    public function recordBulk(BulkAttendanceDTO $dto): void
    {
        $this->repository->recordBulk($dto->attendance_session_id, $dto->attendances);
    }

    public function qrCheckIn(int $sessionId, int $studentId): Attendance
    {
        $presentStatus = AttendanceStatus::where('code', 'PRESENT')->first();
        if (!$presentStatus) {
            throw new \Exception('PRESENT status code not configured.');
        }

        return Attendance::updateOrCreate(
            [
                'attendance_session_id' => $sessionId,
                'student_id' => $studentId,
            ],
            [
                'attendance_status_id' => $presentStatus->id,
                'qr_code_scanned' => true,
                'check_in_time' => now(),
            ]
        );
    }
}
