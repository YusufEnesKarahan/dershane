<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\MaintenanceRepositoryInterface;
use App\Models\MaintenanceRecord;

class MaintenanceRepository implements MaintenanceRepositoryInterface
{
    public function all()
    {
        return MaintenanceRecord::with(['asset', 'employee'])->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return MaintenanceRecord::with(['asset', 'employee'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return MaintenanceRecord::create($data);
    }

    public function update(int $id, array $data)
    {
        $record = MaintenanceRecord::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id)
    {
        $record = MaintenanceRecord::findOrFail($id);
        return $record->delete();
    }
}
