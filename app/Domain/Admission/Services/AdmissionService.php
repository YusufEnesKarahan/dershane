<?php

namespace App\Domain\Admission\Services;

use App\Core\Repositories\Interfaces\AdmissionRepositoryInterface;
use App\DTOs\Admission\CreateAdmissionDTO;
use App\DTOs\Admission\UpdateAdmissionDTO;
use App\Models\StudentAdmission;
use App\Models\AdmissionNote;
use App\Models\Lead;
use App\Models\LeadStatus;

class AdmissionService
{
    public function __construct(protected AdmissionRepositoryInterface $admissionRepo) {}

    public function getAdmissions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->admissionRepo->all();
    }

    public function findAdmission(int $id): ?StudentAdmission
    {
        return $this->admissionRepo->find($id);
    }

    public function createAdmission(CreateAdmissionDTO $dto): StudentAdmission
    {
        return $this->admissionRepo->create($dto);
    }

    public function createFromLead(int $leadId, ?int $userId = null): StudentAdmission
    {
        $lead = Lead::findOrFail($leadId);

        $dto = new CreateAdmissionDTO(
            firstName: $lead->first_name,
            lastName: $lead->last_name,
            phone: $lead->phone,
            leadId: $lead->id,
            branchId: $lead->branch_id,
            advisorId: $lead->advisor_id ?? $userId,
            email: $lead->email,
            school: $lead->school,
            grade: $lead->grade,
            program: $lead->program,
            status: 'lead_converted',
            notes: 'Aday öğrenci satış pipeline sürecinden ön kayıt sistemine dönüştürüldü.'
        );

        $admission = $this->admissionRepo->create($dto);

        // Update lead status to REGISTERED if code exists
        $regStatusId = LeadStatus::where('code', 'REGISTERED')->value('id');
        if ($regStatusId) {
            $lead->update(['lead_status_id' => $regStatusId]);
        }

        return $admission;
    }

    public function updateAdmission(int $id, UpdateAdmissionDTO $dto): bool
    {
        return $this->admissionRepo->update($id, $dto);
    }

    public function updateStatus(int $id, string $status, ?string $description = null, ?int $userId = null): bool
    {
        return $this->admissionRepo->updateStatus($id, $status, $description, $userId);
    }

    public function addNote(int $admissionId, string $noteText, ?int $userId = null): AdmissionNote
    {
        return AdmissionNote::create([
            'student_admission_id' => $admissionId,
            'user_id' => $userId,
            'note_text' => $noteText,
        ]);
    }
}
