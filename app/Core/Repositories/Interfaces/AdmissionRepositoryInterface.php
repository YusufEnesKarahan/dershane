<?php

namespace App\Core\Repositories\Interfaces;

use App\Models\StudentAdmission;
use App\DTOs\Admission\CreateAdmissionDTO;
use App\DTOs\Admission\UpdateAdmissionDTO;

interface AdmissionRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection;

    public function find(int $id): ?StudentAdmission;

    public function create(CreateAdmissionDTO $dto): StudentAdmission;

    public function update(int $id, UpdateAdmissionDTO $dto): bool;

    public function updateStatus(int $id, string $status, ?string $description = null, ?int $userId = null): bool;

    public function delete(int $id): bool;
}
