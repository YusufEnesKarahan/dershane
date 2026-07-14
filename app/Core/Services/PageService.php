<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\PageRepositoryInterface;
use App\DTOs\PageDTO;

class PageService
{
    protected $repository;

    public function __construct(PageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(PageDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, PageDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
