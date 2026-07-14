<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\TeacherRepositoryInterface;
use App\DTOs\TeacherDTO;

class TeacherService
{
    protected $repository;

    public function __construct(TeacherRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(TeacherDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, TeacherDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
