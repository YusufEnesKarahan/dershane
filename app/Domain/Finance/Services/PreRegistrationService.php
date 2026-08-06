<?php

namespace App\Domain\Finance\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PlatformAuditLog;
use App\Models\PreRegistration;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PreRegistrationService
{
    public function createPreRegistration(array $data): PreRegistration
    {
        $preReg = PreRegistration::create([
            'branch_id' => $data['branch_id'] ?? auth()->user()?->branch_id ?? 1,
            'student_name' => $data['student_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'classroom_name' => $data['classroom_name'] ?? null,
            'interested_program' => $data['interested_program'] ?? null,
            'source' => $data['source'] ?? 'Diğer',
            'status' => $data['status'] ?? 'Yeni',
            'assigned_to' => $data['assigned_to'] ?? auth()->id(),
            'notes' => $data['notes'] ?? null,
            'reminder_at' => $data['reminder_at'] ?? null,
        ]);

        $this->logAudit('pre_registration.created', 'PreRegistration', $preReg->id, "Ön kayıt oluşturuldu: {$preReg->student_name}");

        return $preReg;
    }

    public function updatePreRegistration(PreRegistration $preReg, array $data): PreRegistration
    {
        $preReg->update([
            'student_name' => $data['student_name'] ?? $preReg->student_name,
            'phone' => $data['phone'] ?? $preReg->phone,
            'email' => $data['email'] ?? $preReg->email,
            'classroom_name' => $data['classroom_name'] ?? $preReg->classroom_name,
            'interested_program' => $data['interested_program'] ?? $preReg->interested_program,
            'source' => $data['source'] ?? $preReg->source,
            'status' => $data['status'] ?? $preReg->status,
            'assigned_to' => $data['assigned_to'] ?? $preReg->assigned_to,
            'notes' => $data['notes'] ?? $preReg->notes,
            'reminder_at' => $data['reminder_at'] ?? $preReg->reminder_at,
        ]);

        return $preReg;
    }

    /**
     * One-Click Conversion: PreRegistration -> Student -> Guardian -> Invoice
     */
    public function convertToStudent(PreRegistration $preReg, array $data): Student
    {
        return DB::transaction(function () use ($preReg, $data) {
            // 1. Split name into first and last name
            $nameParts = explode(' ', trim($preReg->student_name));
            $lastName = count($nameParts) > 1 ? array_pop($nameParts) : 'Yılmaz';
            $firstName = implode(' ', $nameParts) ?: $preReg->student_name;

            $branchId = $preReg->branch_id ?? auth()->user()?->branch_id ?? 1;
            $studentNumber = $data['student_number'] ?? 'STU-' . rand(10000, 99999);

            // 2. Create Guardian if provided
            $guardianUser = null;
            if (!empty($data['guardian_name']) && !empty($data['guardian_phone'])) {
                $guardianUser = User::create([
                    'branch_id' => $branchId,
                    'name' => $data['guardian_name'],
                    'phone' => $data['guardian_phone'],
                    'email' => $data['guardian_email'] ?? ('veli_' . rand(1000, 9999) . '@dershane.com'),
                    'password' => Hash::make($data['guardian_password'] ?? '12345678'),
                    'status' => \App\Enums\UserStatus::ACTIVE,
                ]);

                $parentRole = Role::where('name', 'Parent')->first();
                if ($parentRole) {
                    $guardianUser->roles()->attach($parentRole->id);
                }
            }

            // 3. Create Student
            $student = Student::create([
                'branch_id' => $branchId,
                'student_number' => $studentNumber,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $preReg->phone,
                'tc_no' => $data['tc_no'] ?? null,
                'classroom_id' => $data['classroom_id'] ?? null,
                'guardian_id' => $guardianUser?->id,
            ]);

            // 4. Create Initial Invoice if amount or items provided
            $amount = (float) ($data['tuition_amount'] ?? $data['total_amount'] ?? 0);
            if ($amount > 0) {
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4));
                $invoice = Invoice::create([
                    'branch_id' => $branchId,
                    'invoice_number' => $invoiceNumber,
                    'student_id' => $student->id,
                    'guardian_id' => $guardianUser?->id,
                    'issue_date' => now()->format('Y-m-d'),
                    'due_date' => $data['due_date'] ?? now()->format('Y-m-d'),
                    'total_amount' => $amount,
                    'paid_amount' => 0.00,
                    'status' => 'Pending',
                    'description' => 'Ön kayıt kesin kayıt dönüşüm faturası',
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'Kayıt Ücreti',
                    'description' => $preReg->interested_program ?: 'Eğitim Kayıt Ücreti',
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'total_price' => $amount,
                ]);
            }

            // 5. Update PreRegistration Status
            $preReg->update([
                'status' => 'Kayıt Oldu',
                'converted_student_id' => $student->id,
            ]);

            $this->logAudit('pre_registration.converted', 'PreRegistration', $preReg->id, "Ön kayıt kesin kayıda dönüştürüldü: {$student->first_name} {$student->last_name} ({$student->student_number})");

            return $student;
        });
    }

    private function logAudit(string $event, string $auditableType, int $auditableId, string $description): void
    {
        PlatformAuditLog::record(auth()->user(), $event, $auditableType, ['description' => $description]);
    }
}
