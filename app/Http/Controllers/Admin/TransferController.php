<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Inventory\Services\AssetTransferService;
use App\Domain\Inventory\Services\AssetService;
use App\Domain\Inventory\Actions\TransferAsset;
use App\DTOs\Inventory\TransferAssetDTO;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct(
        protected AssetTransferService $transferService,
        protected AssetService $assetService,
        protected TransferAsset $transferAction
    ) {}

    public function index()
    {
        $transfers = $this->transferService->allTransfers();
        $assets = $this->assetService->allAssets();
        $locations = $this->assetService->allLocations();
        return view('admin.inventory.transfers', compact('transfers', 'assets', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'to_location_id' => 'required|exists:asset_locations,id',
        ]);

        $dto = TransferAssetDTO::fromRequest($request);
        $this->transferAction->execute($dto);

        return redirect()->route('admin.transfers.index')->with('success', 'Demirbaş başarıyla transfer edildi.');
    }
}
