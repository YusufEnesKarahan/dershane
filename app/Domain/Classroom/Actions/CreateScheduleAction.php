<?php
namespace App\Domain\Classroom\Actions;

use App\DTOs\Classroom\ClassScheduleDTO;
use App\Domain\Classroom\Services\ScheduleService;
use App\Models\ClassSchedule;

class CreateScheduleAction
{
    public function __construct(protected ScheduleService $service) {}

    public function execute(ClassScheduleDTO $dto): ClassSchedule
    {
        return $this->service->createSchedule($dto);
    }
}
