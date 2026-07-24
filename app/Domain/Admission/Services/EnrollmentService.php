<?php

namespace App\Domain\Admission\Services;

use App\Core\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Core\Repositories\Interfaces\AdmissionRepositoryInterface;
use App\DTOs\Admission\EnrollStudentDTO;
use App\Models\StudentEnrollment;
use App\Models\StudentAdmission;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepo,
        protected AdmissionRepositoryInterface $admissionRepo
    ) {}

    public function getEnrollments(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->enrollmentRepo->all();
    }

    public function enrollStudent(EnrollStudentDTO $dto, ?int $userId = null): StudentEnrollment
    {
        return DB::transaction(function () use ($dto, $userId) {
            $admission = StudentAdmission::findOrFail($dto->studentAdmissionId);

            // 1. Create or Find official Student
            $studentNo = 'STD-' . date('Y') . '-' . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT);
            $student = Student::create([
                'first_name' => $admission->first_name,
                'last_name' => $admission->last_name,
                'identity_number' => $admission->tc_no,
                'student_number' => $studentNo,
                'branch_id' => $admission->branch_id,
                'classroom_id' => $dto->classroomId,
                'status' => 'Active',
            ]);

            // 2. Create Finance Invoice & Items
            $finalFee = $dto->finalFee > 0 ? $dto->finalFee : $admission->total_amount;
            $invoiceNo = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
            
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'invoice_number' => $invoiceNo,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'total_amount' => $finalFee,
                'paid_amount' => $admission->deposit_amount,
                'status' => ($admission->deposit_amount >= $finalFee) ? 'Paid' : (($admission->deposit_amount > 0) ? 'Partial' : 'Pending'),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Öğrenci Kayıt ve Eğitim Ücreti - " . ($admission->program ?? 'Genel Program'),
                'quantity' => 1,
                'unit_price' => $finalFee,
                'total_price' => $finalFee,
            ]);

            // Record deposit payment if present
            if ($admission->deposit_amount > 0) {
                Payment::create([
                    'payment_number' => 'PAY-' . date('Ymd') . '-' . rand(1000, 9999),
                    'invoice_id' => $invoice->id,
                    'student_id' => $student->id,
                    'amount' => $admission->deposit_amount,
                    'payment_date' => now(),
                    'notes' => 'Ön kayıt peşinat/kapora tahsilatı',
                    'status' => 'Completed',
                ]);
            }

            // 3. Create StudentEnrollment
            $enrollment = $this->enrollmentRepo->create($dto, $student->id, $invoice->id);

            // 4. Update Admission Workflow state
            $this->admissionRepo->updateStatus($admission->id, 'enrolled', 'Kesin kayıt işlemi tamamlandı ve öğrenci kartı oluşturuldu.', $userId);
            $this->admissionRepo->updateStatus($admission->id, 'active_student', 'Öğrenci faal duruma alındı.', $userId);

            return $enrollment;
        });
    }
}
