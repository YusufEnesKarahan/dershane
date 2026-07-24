<?php

namespace App\Domain\Admission\Services;

use App\Models\ContractTemplate;
use App\Models\EnrollmentContract;
use App\Models\StudentAdmission;
use App\Core\Repositories\Interfaces\AdmissionRepositoryInterface;
use App\DTOs\Admission\CreateContractDTO;

class ContractService
{
    public function __construct(protected AdmissionRepositoryInterface $admissionRepo) {}

    public function getTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return ContractTemplate::where('is_active', true)->get();
    }

    public function generateContract(CreateContractDTO $dto, ?int $userId = null): EnrollmentContract
    {
        $admission = StudentAdmission::with('branch')->findOrFail($dto->studentAdmissionId);
        $template = ContractTemplate::findOrFail($dto->contractTemplateId);

        $placeholders = [
            '{student_name}' => $admission->first_name . ' ' . $admission->last_name,
            '{tc_no}' => $admission->tc_no ?? 'Belirtilmedi',
            '{phone}' => $admission->phone,
            '{program}' => $admission->program ?? 'Genel Program',
            '{total_amount}' => number_format($admission->total_amount, 2) . ' ₺',
            '{deposit_amount}' => number_format($admission->deposit_amount, 2) . ' ₺',
            '{branch_name}' => $admission->branch->name ?? 'Merkez Şube',
            '{date}' => date('d.m.Y'),
        ];

        $renderedContent = str_replace(array_keys($placeholders), array_values($placeholders), $template->content);

        $contractNo = 'CNT-' . date('Y') . '-' . rand(10000, 99999);

        $contract = EnrollmentContract::create([
            'student_admission_id' => $admission->id,
            'contract_template_id' => $template->id,
            'contract_no' => $contractNo,
            'rendered_content' => $renderedContent,
            'status' => 'draft',
        ]);

        $this->admissionRepo->updateStatus(
            $admission->id,
            'contract_ready',
            'Kayıt sözleşmesi dinamik veri ile oluşturuldu ve imzaya hazır hale getirildi.',
            $userId
        );

        return $contract;
    }

    public function signContract(int $contractId, ?int $userId = null): bool
    {
        $contract = EnrollmentContract::findOrFail($contractId);
        $signed = $contract->update([
            'status' => 'signed',
            'signed_at' => now(),
        ]);

        if ($signed) {
            $this->admissionRepo->updateStatus(
                $contract->student_admission_id,
                'payment_pending',
                'Kayıt sözleşmesi taraflarca imzalandı. Ödeme/Finans aşamasına geçildi.',
                $userId
            );
        }

        return $signed;
    }
}
