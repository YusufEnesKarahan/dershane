<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Models\StudentEnrollment;
use App\Models\StudentAdmission;
use App\DTOs\Admission\EnrollStudentDTO;

class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return StudentEnrollment::with(['admission', 'student', 'branch', 'classroom', 'invoice'])->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id): ?StudentEnrollment
    {
        return StudentEnrollment::with(['admission', 'student', 'branch', 'classroom', 'invoice'])->find($id);
    }

    public function create(EnrollStudentDTO $dto, int $studentId, ?int $invoiceId = null): StudentEnrollment
    {
        $admission = StudentAdmission::findOrFail($dto->studentAdmissionId);
        $count = StudentEnrollment::count() + 1;
        $enrollmentNo = 'ENR-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return StudentEnrollment::create([
            'student_admission_id' => $dto->studentAdmissionId,
            'student_id' => $studentId,
            'branch_id' => $admission->branch_id,
            'classroom_id' => $dto->classroomId,
            'invoice_id' => $invoiceId,
            'enrollment_no' => $enrollmentNo,
            'enrollment_date' => now()->toDateString(),
            'academic_year' => $dto->academicYear,
            'final_fee' => $dto->finalFee > 0 ? $dto->finalFee : $admission->total_amount,
            'status' => 'completed',
        ]);
    }
}
