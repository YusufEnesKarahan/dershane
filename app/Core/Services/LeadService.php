<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\LeadRepositoryInterface;
use App\DTOs\LeadDTO;

class LeadService
{
    protected $repository;

    public function __construct(LeadRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(LeadDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, LeadDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
