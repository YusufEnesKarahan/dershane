<?php

namespace App\Domain\CRM\Services;

use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\Core\Repositories\Interfaces\LeadActivityRepositoryInterface;
use App\DTOs\CRM\CreateLeadDTO;
use App\DTOs\CRM\UpdateLeadDTO;
use App\DTOs\CRM\LeadNoteDTO;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadDocument;

class LeadService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepo,
        protected LeadActivityRepositoryInterface $activityRepo
    ) {}

    public function getLeads(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->leadRepo->all();
    }

    public function findLead(int $id): ?Lead
    {
        return $this->leadRepo->find($id);
    }

    public function createLead(CreateLeadDTO $dto): Lead
    {
        $lead = $this->leadRepo->create($dto);
        $this->activityRepo->log($lead->id, 'Created', 'Aday öğrenci kaydı oluşturuldu.', null);
        return $lead;
    }

    public function updateLead(int $id, UpdateLeadDTO $dto): bool
    {
        $updated = $this->leadRepo->update($id, $dto);
        if ($updated) {
            $this->activityRepo->log($id, 'Updated', 'Aday öğrenci bilgileri güncellendi.', null);
        }
        return $updated;
    }

    public function deleteLead(int $id): bool
    {
        return $this->leadRepo->delete($id);
    }

    public function addNote(LeadNoteDTO $dto): LeadNote
    {
        $note = LeadNote::create($dto->toArray());
        $this->activityRepo->log($dto->leadId, 'Called', 'Görüşme notu eklendi: ' . substr($dto->noteText, 0, 50), $dto->userId);
        return $note;
    }

    public function addDocument(int $leadId, string $name, string $filePath, ?int $uploadedBy = null): LeadDocument
    {
        $doc = LeadDocument::create([
            'lead_id' => $leadId,
            'name' => $name,
            'file_path' => $filePath,
            'uploaded_by' => $uploadedBy,
        ]);
        $this->activityRepo->log($leadId, 'Offer', 'Doküman yüklendi: ' . $name, $uploadedBy);
        return $doc;
    }
}
