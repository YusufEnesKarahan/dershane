<?php
namespace App\Domain\Classroom\Services;

use App\DTOs\Classroom\ClassScheduleDTO;
use App\Core\Repositories\Interfaces\ClassScheduleRepositoryInterface;
use App\Models\ClassSchedule;

class ScheduleService
{
    public function __construct(
        protected ClassScheduleRepositoryInterface $repository,
        protected ScheduleConflictService $conflictService
    ) {}

    public function createSchedule(ClassScheduleDTO $dto): ClassSchedule
    {
        $this->conflictService->validateSchedule($dto);

        return $this->repository->create([
            'classroom_id' => $dto->classroom_id,
            'teacher_id' => $dto->teacher_id,
            'course_id' => $dto->course_id,
            'academic_term_id' => $dto->academic_term_id,
            'day_of_week' => $dto->day_of_week,
            'start_time' => $dto->start_time,
            'end_time' => $dto->end_time,
            'color_code' => $dto->color_code,
            'is_active' => $dto->is_active,
        ]);
    }
}
