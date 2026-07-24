<?php

namespace App\Domain\Inventory\Services;

use App\Core\Repositories\Interfaces\AssetRepositoryInterface;
use App\DTOs\Inventory\CreateAssetDTO;
use App\DTOs\Inventory\UpdateAssetDTO;

class AssetService
{
    public function __construct(
        protected AssetRepositoryInterface $assetRepo
    ) {}

    public function allAssets()
    {
        return $this->assetRepo->all();
    }

    public function findAsset(int $id)
    {
        return $this->assetRepo->find($id);
    }

    public function createAsset(CreateAssetDTO $dto)
    {
        return $this->assetRepo->create([
            'category_id' => $dto->categoryId,
            'asset_code' => $dto->assetCode,
            'name' => $dto->name,
            'brand' => $dto->brand,
            'model' => $dto->model,
            'serial_number' => $dto->serialNumber,
            'purchase_date' => $dto->purchaseDate,
            'purchase_price' => $dto->purchasePrice,
            'warranty_end_date' => $dto->warrantyEndDate,
            'status' => $dto->status,
            'location_id' => $dto->locationId,
            'description' => $dto->description,
        ]);
    }

    public function updateAsset(int $id, UpdateAssetDTO $dto)
    {
        return $this->assetRepo->update($id, [
            'category_id' => $dto->categoryId,
            'name' => $dto->name,
            'brand' => $dto->brand,
            'model' => $dto->model,
            'serial_number' => $dto->serialNumber,
            'purchase_date' => $dto->purchaseDate,
            'purchase_price' => $dto->purchasePrice,
            'warranty_end_date' => $dto->warrantyEndDate,
            'status' => $dto->status,
            'location_id' => $dto->locationId,
            'description' => $dto->description,
        ]);
    }

    public function deleteAsset(int $id)
    {
        return $this->assetRepo->delete($id);
    }

    public function retireAsset(int $id)
    {
        return $this->assetRepo->update($id, ['status' => 'retired']);
    }

    public function allCategories()
    {
        return $this->assetRepo->allCategories();
    }

    public function allLocations()
    {
        return $this->assetRepo->allLocations();
    }
}
