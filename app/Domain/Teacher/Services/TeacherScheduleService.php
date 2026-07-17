<?php
namespace App\Domain\Teacher\Services;

use App\DTOs\Teacher\TeacherScheduleDTO;
use App\Core\Repositories\Interfaces\TeacherScheduleRepositoryInterface;
use App\Models\TeacherSchedule;
use Illuminate\Validation\ValidationException;

class TeacherScheduleService
{
    public function __construct(protected TeacherScheduleRepositoryInterface $repository) {}

    public function createSchedule(TeacherScheduleDTO $dto): TeacherSchedule
    {
        // 1. Overlap Conflict Detection Check
        $overlaps = $this->repository->checkOverlap(
            $dto->teacher_id,
            $dto->date,
            $dto->start_time,
            $dto->end_time
        );

        if ($overlaps) {
            throw ValidationException::withMessages([
                'start_time' => 'The teacher already has an overlapping lesson scheduled in this time frame.',
            ]);
        }

        return $this->repository->create([
            'teacher_id' => $dto->teacher_id,
            'classroom_id' => $dto->classroom_id,
            'course_id' => $dto->course_id,
            'date' => $dto->date,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
        ]);
    }
}
