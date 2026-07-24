<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\PurchaseService;
use App\Domain\Inventory\Services\SupplierService;
use App\Domain\Inventory\Actions\CreatePurchaseOrder;
use App\Domain\Inventory\Actions\ApprovePurchaseOrder;
use App\DTOs\Inventory\CreatePurchaseOrderDTO;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService,
        protected SupplierService $supplierService,
        protected CreatePurchaseOrder $createAction,
        protected ApprovePurchaseOrder $approveAction
    ) {}

    public function index()
    {
        $orders = $this->purchaseService->allOrders();
        $suppliers = $this->supplierService->allSuppliers();
        return view('admin.inventory.purchase', compact('orders', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_number' => 'required|string|unique:purchase_orders,order_number',
            'total_amount' => 'required|numeric',
        ]);

        $dto = CreatePurchaseOrderDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('admin.purchase.index')->with('success', 'Satın alma talebi oluşturuldu.');
    }

    public function approve(int $id)
    {
        $this->approveAction->execute($id);
        return redirect()->route('admin.purchase.index')->with('success', 'Satın alma talebi onaylandı.');
    }

    public function complete(int $id)
    {
        $this->purchaseService->completeOrder($id);
        return redirect()->route('admin.purchase.index')->with('success', 'Satın alma tamamlandı olarak işaretlendi.');
    }
}
