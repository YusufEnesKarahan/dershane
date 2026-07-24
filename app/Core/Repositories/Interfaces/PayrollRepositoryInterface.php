<?php

namespace App\Core\Repositories\Interfaces;

interface PayrollRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getForEmployee(int $employeeId);
}
