<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\InventoryRepositoryInterface;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use App\Models\PurchaseOrder;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function all()
    {
        return InventoryItem::with(['category', 'warehouse'])->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return InventoryItem::with(['category', 'warehouse', 'transactions.creator'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return InventoryItem::create($data);
    }

    public function update(int $id, array $data)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete(int $id)
    {
        $item = InventoryItem::findOrFail($id);
        return $item->delete();
    }

    public function allCategories()
    {
        return InventoryCategory::withCount('items')->orderBy('id', 'desc')->get();
    }

    public function allWarehouses()
    {
        return Warehouse::with('branch')->orderBy('id', 'desc')->get();
    }

    public function createTransaction(array $data)
    {
        return InventoryTransaction::create($data);
    }

    public function getTransactions(?int $itemId = null)
    {
        $query = InventoryTransaction::with(['item', 'creator']);
        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        return $query->orderBy('id', 'desc')->get();
    }

    public function allPurchaseOrders()
    {
        return PurchaseOrder::with('supplier')->orderBy('id', 'desc')->get();
    }

    public function findPurchaseOrder(int $id)
    {
        return PurchaseOrder::with('supplier')->findOrFail($id);
    }

    public function createPurchaseOrder(array $data)
    {
        return PurchaseOrder::create($data);
    }

    public function updatePurchaseOrder(int $id, array $data)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->update($data);
        return $order;
    }
}
