<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\AdmissionRepositoryInterface;
use App\Models\StudentAdmission;
use App\Models\AdmissionStatusLog;
use App\DTOs\Admission\CreateAdmissionDTO;
use App\DTOs\Admission\UpdateAdmissionDTO;

class AdmissionRepository implements AdmissionRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return StudentAdmission::with(['lead', 'branch', 'advisor', 'documents', 'enrollment'])->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id): ?StudentAdmission
    {
        return StudentAdmission::with([
            'lead', 'branch', 'advisor', 'documents.uploader', 'documents.approver',
            'admissionNotes.user', 'statusLogs.user', 'contracts.template',
            'enrollment.student', 'enrollment.classroom', 'payments.receiver'
        ])->find($id);
    }

    public function create(CreateAdmissionDTO $dto): StudentAdmission
    {
        $count = StudentAdmission::count() + 1;
        $admissionNo = 'ADM-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        $data = $dto->toArray();
        $data['admission_no'] = $admissionNo;

        $admission = StudentAdmission::create($data);

        AdmissionStatusLog::create([
            'student_admission_id' => $admission->id,
            'from_status' => null,
            'to_status' => $admission->status,
            'description' => 'Ön kayıt başvurusu ve kaydı başlatıldı.',
            'user_id' => $dto->advisorId,
        ]);

        return $admission;
    }

    public function update(int $id, UpdateAdmissionDTO $dto): bool
    {
        $admission = StudentAdmission::findOrFail($id);
        $oldStatus = $admission->status;
        $updated = $admission->update($dto->toArray());

        if ($updated && $dto->status && $dto->status !== $oldStatus) {
            AdmissionStatusLog::create([
                'student_admission_id' => $admission->id,
                'from_status' => $oldStatus,
                'to_status' => $dto->status,
                'description' => 'Ön kayıt başvuru bilgileri güncellendi.',
                'user_id' => $dto->advisorId,
            ]);
        }

        return $updated;
    }

    public function updateStatus(int $id, string $status, ?string $description = null, ?int $userId = null): bool
    {
        $admission = StudentAdmission::findOrFail($id);
        $oldStatus = $admission->status;
        
        if ($oldStatus === $status) {
            return true;
        }

        $updated = $admission->update(['status' => $status]);

        if ($updated) {
            AdmissionStatusLog::create([
                'student_admission_id' => $admission->id,
                'from_status' => $oldStatus,
                'to_status' => $status,
                'description' => $description ?? "Başvuru durumu {$status} olarak güncellendi.",
                'user_id' => $userId,
            ]);
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $admission = StudentAdmission::findOrFail($id);
        return $admission->delete();
    }
}
