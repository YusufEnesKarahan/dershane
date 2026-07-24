<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Models\Supplier;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function all()
    {
        return Supplier::withCount('purchaseOrders')->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return Supplier::with('purchaseOrders')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Supplier::create($data);
    }

    public function update(int $id, array $data)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($data);
        return $supplier;
    }

    public function delete(int $id)
    {
        $supplier = Supplier::findOrFail($id);
        return $supplier->delete();
    }
}
