<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\InventoryRepositoryInterface;
use App\DTOs\Inventory\CreateInventoryItemDTO;

class InventoryService
{
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepo
    ) {}

    public function allItems()
    {
        return $this->inventoryRepo->all();
    }

    public function findItem(int $id)
    {
        return $this->inventoryRepo->find($id);
    }

    public function createItem(CreateInventoryItemDTO $dto)
    {
        return $this->inventoryRepo->create([
            'category_id' => $dto->categoryId,
            'warehouse_id' => $dto->warehouseId,
            'name' => $dto->name,
            'sku' => $dto->sku,
            'unit' => $dto->unit,
            'quantity' => $dto->quantity,
            'minimum_quantity' => $dto->minimumQuantity,
            'description' => $dto->description
        ]);
    }

    public function updateItem(int $id, array $data)
    {
        return $this->inventoryRepo->update($id, $data);
    }

    public function deleteItem(int $id)
    {
        return $this->inventoryRepo->delete($id);
    }

    public function allCategories()
    {
        return $this->inventoryRepo->allCategories();
    }

    public function allWarehouses()
    {
        return $this->inventoryRepo->allWarehouses();
    }
}
