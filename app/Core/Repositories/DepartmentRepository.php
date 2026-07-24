<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function all()
    {
        return Department::with(['manager', 'positions'])->get();
    }

    public function find(int $id)
    {
        return Department::with(['manager', 'positions'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function update(int $id, array $data)
    {
        $dept = Department::findOrFail($id);
        $dept->update($data);
        return $dept;
    }

    public function delete(int $id)
    {
        $dept = Department::findOrFail($id);
        return $dept->delete();
    }
}
