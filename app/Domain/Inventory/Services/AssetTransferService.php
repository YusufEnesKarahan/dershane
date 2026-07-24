<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\TransferRepositoryInterface;
use App\Core\Repositories\Interfaces\AssetRepositoryInterface;
use App\DTOs\Inventory\TransferAssetDTO;

class AssetTransferService
{
    public function __construct(
        protected TransferRepositoryInterface $transferRepo,
        protected AssetRepositoryInterface $assetRepo
    ) {}

    public function allTransfers()
    {
        return $this->transferRepo->all();
    }

    public function transferAsset(TransferAssetDTO $dto)
    {
        // Relocate asset to new location
        $this->assetRepo->update($dto->assetId, ['location_id' => $dto->toLocationId]);

        return $this->transferRepo->create([
            'asset_id' => $dto->assetId,
            'from_location_id' => $dto->fromLocationId,
            'to_location_id' => $dto->toLocationId,
            'transfer_date' => $dto->transferDate,
            'notes' => $dto->notes
        ]);
    }
}
