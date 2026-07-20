<?php
namespace App\Domain\Attendance\Actions;

use App\DTOs\Attendance\CreateAttendanceSessionDTO;
use App\Domain\Attendance\Services\AttendanceSessionService;
use App\Models\AttendanceSession;

class CreateAttendanceSessionAction
{
    public function __construct(protected AttendanceSessionService $service) {}

    public function execute(CreateAttendanceSessionDTO $dto): AttendanceSession
    {
        return $this->service->createSession($dto);
    }
}
