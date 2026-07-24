<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssetService;
use App\DTOs\Inventory\UpdateAssetDTO;

class UpdateAsset
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function execute(int $id, UpdateAssetDTO $dto)
    {
        return $this->assetService->updateAsset($id, $dto);
    }
}
