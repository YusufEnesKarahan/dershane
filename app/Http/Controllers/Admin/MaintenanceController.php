<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\MaintenanceService;
use App\Domain\Inventory\Services\AssetService;
use App\Domain\Inventory\Actions\CreateMaintenanceRecord;
use App\DTOs\Inventory\MaintenanceDTO;
use App\Models\Employee;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService,
        protected AssetService $assetService,
        protected CreateMaintenanceRecord $createAction
    ) {}

    public function index()
    {
        $records = $this->maintenanceService->allRecords();
        $assets = $this->assetService->allAssets();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.inventory.maintenance', compact('records', 'assets', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'cost' => 'required|numeric',
        ]);

        $dto = MaintenanceDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('admin.maintenance.index')->with('success', 'Bakım kaydı başarıyla eklendi.');
    }

    public function complete(int $id)
    {
        $this->maintenanceService->completeMaintenance($id);
        return redirect()->route('admin.maintenance.index')->with('success', 'Bakım kaydı tamamlandı olarak işaretlendi.');
    }
}
