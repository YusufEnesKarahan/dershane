<?php
namespace App\Domain\Classroom\Actions;

use App\DTOs\Classroom\HolidayDTO;
use App\Domain\Classroom\Services\HolidayService;
use App\Models\Holiday;

class CreateHolidayAction
{
    public function __construct(protected HolidayService $service) {}

    public function execute(HolidayDTO $dto): Holiday
    {
        return $this->service->createHoliday($dto);
    }
}
