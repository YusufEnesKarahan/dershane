<?php
namespace App\Domain\Attendance\Actions;

use App\DTOs\Attendance\BulkAttendanceDTO;
use App\Domain\Attendance\Services\AttendanceService;

class RecordBulkAttendanceAction
{
    public function __construct(protected AttendanceService $service) {}

    public function execute(BulkAttendanceDTO $dto): void
    {
        $this->service->recordBulk($dto);
    }
}
