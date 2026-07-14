<?php
namespace App\Core\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function bulkDelete(array $ids): int;
    public function bulkRestore(array $ids): int;
    public function bulkUpdate(array $ids, array $data): int;
    public function bulkStatus(array $ids, string $status): int;
}
