<?php
namespace App\Domain\Student\Services;

use App\DTOs\Student\StudentEnrollmentDTO;
use App\Models\StudentEnrollment;
use App\Models\Course;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function enroll(StudentEnrollmentDTO $dto): StudentEnrollment
    {
        // 1. Mükerrer kurs kaydı engeli
        $exists = StudentEnrollment::where('student_id', $dto->student_id)
            ->where('course_id', $dto->course_id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'course_id' => 'Öğrenci bu kursa zaten kayıtlıdır.',
            ]);
        }

        // 2. Kontenjan Kontrolü
        $course = Course::find($dto->course_id);
        if ($course && $course->capacity > 0) {
            $currentEnrolled = StudentEnrollment::where('course_id', $dto->course_id)->count();
            if ($currentEnrolled >= $course->capacity) {
                throw ValidationException::withMessages([
                    'course_id' => 'Bu kursun maksimum kontenjanına ulaşılmıştır.',
                ]);
            }
        }

        return StudentEnrollment::create([
            'student_id' => $dto->student_id,
            'course_id' => $dto->course_id,
            'academic_term_id' => $dto->academic_term_id,
            'price_paid' => $dto->price_paid,
            'enrollment_date' => $dto->enrollment_date,
            'status' => 'Active',
        ]);
    }
}
