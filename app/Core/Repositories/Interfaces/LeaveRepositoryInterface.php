<?php

namespace App\Core\Repositories\Interfaces;

interface LeaveRepositoryInterface
{
    public function allTypes();
    public function allRequests();
    public function findRequest(int $id);
    public function createRequest(array $data);
    public function updateRequest(int $id, array $data);
    public function getRequestsForEmployee(int $employeeId);
}
