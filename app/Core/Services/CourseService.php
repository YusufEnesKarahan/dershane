<?php
namespace App\Core\Services;
use App\Core\Repositories\Interfaces\CourseRepositoryInterface;
use App\DTOs\CourseDTO;

class CourseService
{
    protected $repository;

    public function __construct(CourseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(CourseDTO $dto)
    {
        return $this->repository->create($dto->data);
    }

    public function update($id, CourseDTO $dto)
    {
        return $this->repository->update($id, $dto->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
