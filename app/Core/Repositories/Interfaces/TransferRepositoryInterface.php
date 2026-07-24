<?php

namespace App\Core\Repositories\Interfaces;

interface TransferRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function delete(int $id);
}
