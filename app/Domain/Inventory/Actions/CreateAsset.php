<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssetService;
use App\DTOs\Inventory\CreateAssetDTO;

class CreateAsset
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function execute(CreateAssetDTO $dto)
    {
        return $this->assetService->createAsset($dto);
    }
}
