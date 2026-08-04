<?php

namespace App\Domain\Academic\Services;

use App\Models\LessonSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class LessonScheduleManagementService
{
    /**
     * Create a new lesson schedule.
     */
    public function createSchedule(array $data): LessonSchedule
    {
        return DB::transaction(function () use ($data) {
            $this->validateConflicts(
                $data['branch_id'],
                $data['day_of_week'],
                $data['start_time'],
                $data['end_time'],
                $data['teacher_id'] ?? null,
                $data['classroom_id'],
                $data['room'] ?? null
            );

            $schedule = LessonSchedule::create($data);

            if (!empty($data['additional_teachers'])) {
                $schedule->additionalTeachers()->sync($data['additional_teachers']);
            }

            return $schedule;
        });
    }

    /**
     * Update an existing lesson schedule.
     */
    public function updateSchedule(LessonSchedule $schedule, array $data): LessonSchedule
    {
        return DB::transaction(function () use ($schedule, $data) {
            $this->validateConflicts(
                $schedule->branch_id,
                $data['day_of_week'] ?? $schedule->day_of_week,
                $data['start_time'] ?? $schedule->start_time,
                $data['end_time'] ?? $schedule->end_time,
                $data['teacher_id'] ?? $schedule->teacher_id,
                $data['classroom_id'] ?? $schedule->classroom_id,
                $data['room'] ?? $schedule->room,
                $schedule->id
            );

            $schedule->update($data);

            if (isset($data['additional_teachers'])) {
                $schedule->additionalTeachers()->sync($data['additional_teachers']);
            }

            return $schedule;
        });
    }

    /**
     * Soft delete a lesson schedule.
     */
    public function deleteSchedule(LessonSchedule $schedule): bool
    {
        return DB::transaction(function () use ($schedule) {
            return $schedule->delete();
        });
    }

    /**
     * Assign an additional teacher to a schedule.
     */
    public function assignTeacher(LessonSchedule $schedule, int $teacherId): void
    {
        DB::transaction(function () use ($schedule, $teacherId) {
            $this->validateTeacherConflict(
                $schedule->branch_id,
                $teacherId,
                $schedule->day_of_week,
                $schedule->start_time,
                $schedule->end_time,
                $schedule->id
            );
            $schedule->additionalTeachers()->syncWithoutDetaching([$teacherId]);
        });
    }

    /**
     * Change the primary teacher of a schedule.
     */
    public function changeTeacher(LessonSchedule $schedule, int $newTeacherId): LessonSchedule
    {
        return DB::transaction(function () use ($schedule, $newTeacherId) {
            $this->validateTeacherConflict(
                $schedule->branch_id,
                $newTeacherId,
                $schedule->day_of_week,
                $schedule->start_time,
                $schedule->end_time,
                $schedule->id
            );
            $schedule->update(['teacher_id' => $newTeacherId]);
            return $schedule;
        });
    }

    /**
     * Duplicate a given week's schedules to another week (if dates were used, but we use day_of_week).
     * For a typical recurring weekly schedule, duplication might mean copying to another term or subset.
     */
    public function duplicateWeek(int $branchId, int $sourceTermId, int $targetTermId): void
    {
        // Copy logic for terms
        $this->copyAcademicTerm($branchId, $sourceTermId, $targetTermId);
    }

    /**
     * Copy all schedules from one academic term to another.
     */
    public function copyAcademicTerm(int $branchId, int $sourceTermId, int $targetTermId): void
    {
        DB::transaction(function () use ($branchId, $sourceTermId, $targetTermId) {
            $sourceSchedules = LessonSchedule::where('branch_id', $branchId)
                ->where('academic_term_id', $sourceTermId)
                ->get();

            foreach ($sourceSchedules as $schedule) {
                // Ensure no conflict in the target term
                $this->validateConflicts(
                    $branchId,
                    $schedule->day_of_week,
                    $schedule->start_time,
                    $schedule->end_time,
                    $schedule->teacher_id,
                    $schedule->classroom_id,
                    $schedule->room
                );

                $newSchedule = $schedule->replicate();
                $newSchedule->academic_term_id = $targetTermId;
                $newSchedule->save();

                $additionalTeachers = $schedule->additionalTeachers()->pluck('teachers.id')->toArray();
                if (!empty($additionalTeachers)) {
                    $newSchedule->additionalTeachers()->sync($additionalTeachers);
                }
            }
        });
    }

    /**
     * Master conflict validator
     */
    protected function validateConflicts(int $branchId, string $day, string $start, string $end, ?int $teacherId, int $classroomId, ?string $room, ?int $excludeId = null): void
    {
        if ($teacherId) {
            $this->validateTeacherConflict($branchId, $teacherId, $day, $start, $end, $excludeId);
        }
        $this->validateClassroomConflict($branchId, $classroomId, $day, $start, $end, $excludeId);
        
        if ($room) {
            $this->validateRoomConflict($branchId, $room, $day, $start, $end, $excludeId);
        }
    }

    public function validateTeacherConflict(int $branchId, int $teacherId, string $day, string $start, string $end, ?int $excludeId = null): void
    {
        $conflict = LessonSchedule::where('branch_id', $branchId)
            ->where('day_of_week', $day)
            ->where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('additionalTeachers', function($q) use ($teacherId) {
                          $q->where('teachers.id', $teacherId);
                      });
            })
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('start_time', '<', $end)
                       ->where('end_time', '>', $start);
                });
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($conflict) {
            throw new Exception('Öğretmen bu saat diliminde başka bir derse atanmış.');
        }
    }

    public function validateClassroomConflict(int $branchId, int $classroomId, string $day, string $start, string $end, ?int $excludeId = null): void
    {
        $conflict = LessonSchedule::where('branch_id', $branchId)
            ->where('classroom_id', $classroomId)
            ->where('day_of_week', $day)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($conflict) {
            throw new Exception('Sınıf bu saat diliminde başka bir derse atanmış.');
        }
    }

    public function validateRoomConflict(int $branchId, string $room, string $day, string $start, string $end, ?int $excludeId = null): void
    {
        $conflict = LessonSchedule::where('branch_id', $branchId)
            ->where('room', $room)
            ->where('day_of_week', $day)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($conflict) {
            throw new Exception('Derslik/Salon bu saat diliminde dolu.');
        }
    }

    public function getWeeklySchedule(int $branchId, int $academicTermId)
    {
        return LessonSchedule::with(['teacher', 'course', 'classroom'])
            ->where('branch_id', $branchId)
            ->where('academic_term_id', $academicTermId)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }

    public function getTeacherSchedule(int $branchId, int $teacherId, int $academicTermId)
    {
        return LessonSchedule::with(['course', 'classroom'])
            ->where('branch_id', $branchId)
            ->where('academic_term_id', $academicTermId)
            ->where(function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                      ->orWhereHas('additionalTeachers', function($q) use ($teacherId) {
                          $q->where('teachers.id', $teacherId);
                      });
            })
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }

    public function getClassroomSchedule(int $branchId, int $classroomId, int $academicTermId)
    {
        return LessonSchedule::with(['teacher', 'course'])
            ->where('branch_id', $branchId)
            ->where('academic_term_id', $academicTermId)
            ->where('classroom_id', $classroomId)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }
}
