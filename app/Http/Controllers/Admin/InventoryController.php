<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\InventoryService;
use App\Domain\Inventory\Services\StockMovementService;
use App\Domain\Inventory\Actions\CreateInventoryTransaction;
use App\DTOs\Inventory\CreateInventoryItemDTO;
use App\DTOs\Inventory\InventoryTransactionDTO;
use App\Models\InventoryCategory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected StockMovementService $stockMovementService,
        protected CreateInventoryTransaction $transactionAction
    ) {}

    public function index()
    {
        $items = $this->inventoryService->allItems();
        $categories = InventoryCategory::all();
        $warehouses = Warehouse::all();
        $transactions = $this->stockMovementService->allTransactions();
        return view('admin.inventory.inventory', compact('items', 'categories', 'warehouses', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory_items,sku',
            'category_id' => 'required|exists:inventory_categories,id',
        ]);

        $dto = CreateInventoryItemDTO::fromRequest($request);
        $this->inventoryService->createItem($dto);

        return redirect()->route('admin.inventory.index')->with('success', 'Stok ürünü eklendi.');
    }

    public function transaction(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'type' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $dto = InventoryTransactionDTO::fromRequest($request, Auth::id());
        $this->transactionAction->execute($dto);

        return redirect()->route('admin.inventory.index')->with('success', 'Stok hareketi işlendi.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:inventory_categories,code',
        ]);

        InventoryCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Stok kategorisi eklendi.');
    }

    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Warehouse::create([
            'branch_id' => $request->branch_id ? (int) $request->branch_id : null,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Depo eklendi.');
    }
}
