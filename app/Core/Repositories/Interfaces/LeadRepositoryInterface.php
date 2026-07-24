<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\Lead;
use App\DTOs\CRM\CreateLeadDTO;
use App\DTOs\CRM\UpdateLeadDTO;

interface LeadRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function find(int $id): ?Lead;

    public function create(CreateLeadDTO $dto): Lead;

    public function update(int $id, UpdateLeadDTO $dto): bool;

    public function delete(int $id): bool;

    public function updateStatus(int $id, int $statusId): bool;

    public function assign(int $id, ?int $advisorId, ?int $branchId): bool;
}
