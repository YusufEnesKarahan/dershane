<?php

namespace App\Core\Repositories\Interfaces;

interface InventoryRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function allCategories();
    public function allWarehouses();
    public function createTransaction(array $data);
    public function getTransactions(?int $itemId = null);
    public function allPurchaseOrders();
    public function findPurchaseOrder(int $id);
    public function createPurchaseOrder(array $data);
    public function updatePurchaseOrder(int $id, array $data);
}
