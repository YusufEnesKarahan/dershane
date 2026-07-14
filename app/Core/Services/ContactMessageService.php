<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\ContactMessageRepositoryInterface;
use App\DTOs\ContactMessageDTO;

class ContactMessageService
{
    protected $repository;

    public function __construct(ContactMessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(ContactMessageDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, ContactMessageDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
