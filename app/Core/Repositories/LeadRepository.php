<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use App\DTOs\CRM\CreateLeadDTO;
use App\DTOs\CRM\UpdateLeadDTO;

class LeadRepository implements LeadRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Lead::with(['source', 'status', 'branch', 'advisor'])->orderBy('created_at', 'desc')->get();
    }

    public function find(int $id): ?Lead
    {
        return Lead::with(['source', 'status', 'branch', 'advisor', 'notes.user', 'activities.user', 'followups.user', 'documents.uploader', 'tags'])->find($id);
    }

    public function create(CreateLeadDTO $dto): Lead
    {
        return Lead::create($dto->toArray());
    }

    public function update(int $id, UpdateLeadDTO $dto): bool
    {
        $lead = Lead::findOrFail($id);
        return $lead->update($dto->toArray());
    }

    public function delete(int $id): bool
    {
        $lead = Lead::findOrFail($id);
        return $lead->delete();
    }

    public function updateStatus(int $id, int $statusId): bool
    {
        $lead = Lead::findOrFail($id);
        return $lead->update(['lead_status_id' => $statusId]);
    }

    public function assign(int $id, ?int $advisorId, ?int $branchId): bool
    {
        $lead = Lead::findOrFail($id);
        $data = [];
        if ($advisorId !== null) {
            $data['advisor_id'] = $advisorId;
        }
        if ($branchId !== null) {
            $data['branch_id'] = $branchId;
        }
        return $lead->update($data);
    }
}
