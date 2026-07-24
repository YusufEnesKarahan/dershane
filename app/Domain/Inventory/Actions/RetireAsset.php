<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Services\AssetService;

class RetireAsset
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    public function execute(int $id)
    {
        return $this->assetService->retireAsset($id);
    }
}
