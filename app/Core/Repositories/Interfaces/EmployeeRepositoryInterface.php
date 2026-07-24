<?php

namespace App\Core\Repositories\Interfaces;

interface EmployeeRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findByEmployeeNo(string $employeeNo);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
