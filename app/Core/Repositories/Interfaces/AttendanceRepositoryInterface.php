<?php

namespace App\Core\Repositories\Interfaces;

interface AttendanceRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function getForEmployee(int $employeeId, ?string $startDate = null, ?string $endDate = null);
}
