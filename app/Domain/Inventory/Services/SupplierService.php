<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\SupplierRepositoryInterface;
use App\DTOs\Inventory\CreateSupplierDTO;

class SupplierService
{
    public function __construct(
        protected SupplierRepositoryInterface $supplierRepo
    ) {}

    public function allSuppliers()
    {
        return $this->supplierRepo->all();
    }

    public function findSupplier(int $id)
    {
        return $this->supplierRepo->find($id);
    }

    public function createSupplier(CreateSupplierDTO $dto)
    {
        return $this->supplierRepo->create([
            'name' => $dto->name,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'address' => $dto->address,
            'tax_number' => $dto->taxNumber
        ]);
    }

    public function updateSupplier(int $id, array $data)
    {
        return $this->supplierRepo->update($id, $data);
    }

    public function deleteSupplier(int $id)
    {
        return $this->supplierRepo->delete($id);
    }
}
