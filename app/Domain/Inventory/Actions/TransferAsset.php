<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssetTransferService;
use App\DTOs\Inventory\TransferAssetDTO;

class TransferAsset
{
    public function __construct(
        protected AssetTransferService $transferService
    ) {}

    public function execute(TransferAssetDTO $dto)
    {
        return $this->transferService->transferAsset($dto);
    }
}
