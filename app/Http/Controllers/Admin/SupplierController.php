<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\SupplierService;
use App\DTOs\Inventory\CreateSupplierDTO;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {}

    public function index()
    {
        $suppliers = $this->supplierService->allSuppliers();
        return view('admin.inventory.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $dto = CreateSupplierDTO::fromRequest($request);
        $this->supplierService->createSupplier($dto);

        return redirect()->route('admin.suppliers.index')->with('success', 'Tedarikçi eklendi.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->supplierService->updateSupplier($id, $request->all());

        return redirect()->route('admin.suppliers.index')->with('success', 'Tedarikçi güncellendi.');
    }

    public function destroy(int $id)
    {
        $this->supplierService->deleteSupplier($id);
        return redirect()->route('admin.suppliers.index')->with('success', 'Tedarikçi silindi.');
    }
}
