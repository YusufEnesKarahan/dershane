<?php
namespace App\Domain\Classroom\Services;

use App\Models\ClassSchedule;
use App\Models\Holiday;
use App\Models\Classroom;
use App\Models\Course;
use App\DTOs\Classroom\ClassScheduleDTO;
use Illuminate\Validation\ValidationException;

class ScheduleConflictService
{
    public function validateSchedule(ClassScheduleDTO $dto): void
    {
        // 1. Derslik Saat Çakışması Kontrolü
        $classroomConflict = ClassSchedule::where('classroom_id', $dto->classroom_id)
            ->where('day_of_week', $dto->day_of_week)
            ->where(function ($query) use ($dto) {
                $query->whereBetween('start_time', [$dto->start_time, $dto->end_time])
                      ->orWhereBetween('end_time', [$dto->start_time, $dto->end_time])
                      ->orWhere(function ($q) use ($dto) {
                          $q->where('start_time', '<=', $dto->start_time)
                            ->where('end_time', '>=', $dto->end_time);
                      });
            })->exists();

        if ($classroomConflict) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Seçilen derslik belirtilen gün ve saat bloğunda doludur.',
            ]);
        }

        // 2. Eğitmen Saat Çakışması Kontrolü
        $teacherConflict = ClassSchedule::where('teacher_id', $dto->teacher_id)
            ->where('day_of_week', $dto->day_of_week)
            ->where(function ($query) use ($dto) {
                $query->whereBetween('start_time', [$dto->start_time, $dto->end_time])
                      ->orWhereBetween('end_time', [$dto->start_time, $dto->end_time])
                      ->orWhere(function ($q) use ($dto) {
                          $q->where('start_time', '<=', $dto->start_time)
                            ->where('end_time', '>=', $dto->end_time);
                      });
            })->exists();

        if ($teacherConflict) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Seçilen eğitmenin belirtilen saatlerde başka bir sınıfta dersi mevcuttur.',
            ]);
        }

        // 3. Kapasite Kontrol Uyarısı
        $classroom = Classroom::find($dto->classroom_id);
        $course = Course::find($dto->course_id);
        if ($classroom && $course && $course->capacity > $classroom->capacity) {
            // Log warning or throw if strict mode
        }
    }
}
