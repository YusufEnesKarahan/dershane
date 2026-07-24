<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\AssetService;
use App\Domain\Inventory\Services\AssignmentService;
use App\Domain\Inventory\Actions\CreateAsset;
use App\Domain\Inventory\Actions\UpdateAsset;
use App\Domain\Inventory\Actions\AssignAsset;
use App\Domain\Inventory\Actions\ReturnAsset;
use App\Domain\Inventory\Actions\RetireAsset;
use App\DTOs\Inventory\CreateAssetDTO;
use App\DTOs\Inventory\UpdateAssetDTO;
use App\DTOs\Inventory\AssignAssetDTO;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService,
        protected AssignmentService $assignmentService,
        protected CreateAsset $createAction,
        protected UpdateAsset $updateAction,
        protected AssignAsset $assignAction,
        protected ReturnAsset $returnAction,
        protected RetireAsset $retireAction
    ) {}

    public function index()
    {
        $assets = $this->assetService->allAssets();
        $categories = $this->assetService->allCategories();
        $locations = $this->assetService->allLocations();
        $employees = Employee::where('employment_status', 'Active')->get();
        return view('admin.inventory.assets', compact('assets', 'categories', 'locations', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'asset_code' => 'required|string|unique:assets,asset_code',
        ]);

        $dto = CreateAssetDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('admin.assets.index')->with('success', 'Demirbaş başarıyla eklendi.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
        ]);

        $dto = UpdateAssetDTO::fromRequest($request);
        $this->updateAction->execute($id, $dto);

        return redirect()->route('admin.assets.index')->with('success', 'Demirbaş başarıyla güncellendi.');
    }

    public function destroy(int $id)
    {
        $this->assetService->deleteAsset($id);
        return redirect()->route('admin.assets.index')->with('success', 'Demirbaş silindi.');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $dto = AssignAssetDTO::fromRequest($request);
        $this->assignAction->execute($dto);

        return redirect()->route('admin.assets.index')->with('success', 'Zimmet başarıyla atandı.');
    }

    public function returnAssignment(Request $request, int $id)
    {
        $this->returnAction->execute(
            $id,
            $request->input('returned_date'),
            $request->input('condition'),
            $request->input('notes')
        );

        return redirect()->route('admin.assets.index')->with('success', 'Zimmet iade alındı.');
    }

    public function retire(int $id)
    {
        $this->retireAction->execute($id);
        return redirect()->route('admin.assets.index')->with('success', 'Demirbaş emekliye ayrıldı.');
    }
}
