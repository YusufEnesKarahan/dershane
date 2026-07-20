<?php
namespace App\Domain\Classroom\Services;

use App\DTOs\Classroom\HolidayDTO;
use App\Models\Holiday;

class HolidayService
{
    public function createHoliday(HolidayDTO $dto): Holiday
    {
        return Holiday::create([
            'name' => $dto->name,
            'start_date' => $dto->start_date,
            'end_date' => $dto->end_date,
            'branch_id' => $dto->branch_id,
            'description' => $dto->description,
        ]);
    }
}
