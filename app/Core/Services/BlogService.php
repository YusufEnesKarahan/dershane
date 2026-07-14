<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\BlogRepositoryInterface;
use App\DTOs\BlogDTO;

class BlogService
{
    protected $repository;

    public function __construct(BlogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(BlogDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, BlogDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
