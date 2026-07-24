<?php

namespace App\Domain\HR\Services;

use App\Core\Repositories\Interfaces\AttendanceRepositoryInterface;
use Carbon\Carbon;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepo
    ) {}

    public function allAttendances()
    {
        return $this->attendanceRepo->all();
    }

    public function logAttendance(int $employeeId, string $date, string $checkIn, ?string $checkOut = null)
    {
        $checkInTime = Carbon::createFromFormat('H:i:s', $checkIn);
        $standardIn = Carbon::createFromFormat('H:i:s', '09:00:00');
        $lateMinutes = 0;
        if ($checkInTime->greaterThan($standardIn)) {
            $lateMinutes = $checkInTime->diffInMinutes($standardIn);
        }

        $workedMinutes = 0;
        $overtimeMinutes = 0;
        if ($checkOut) {
            $checkOutTime = Carbon::createFromFormat('H:i:s', $checkOut);
            $workedMinutes = $checkOutTime->diffInMinutes($checkInTime);
            if ($workedMinutes > 480) { // 8 hours standard
                $overtimeMinutes = $workedMinutes - 480;
            }
        }

        return $this->attendanceRepo->create([
            'employee_id' => $employeeId,
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'worked_minutes' => $workedMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'late_minutes' => $lateMinutes,
        ]);
    }
}
