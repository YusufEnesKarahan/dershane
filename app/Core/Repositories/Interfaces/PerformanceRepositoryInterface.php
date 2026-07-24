<?php

namespace App\Core\Repositories\Interfaces;

interface PerformanceRepositoryInterface
{
    public function allReviews();
    public function findReview(int $id);
    public function createReview(array $data);
    public function updateReview(int $id, array $data);
    public function getForEmployee(int $employeeId);
}
