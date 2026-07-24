<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\AssetRepositoryInterface;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\AssetAssignment;

class AssetRepository implements AssetRepositoryInterface
{
    public function all()
    {
        return Asset::with(['category', 'location'])->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return Asset::with(['category', 'location', 'assignments.employee', 'maintenanceRecords.employee', 'transfers.fromLocation', 'transfers.toLocation'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Asset::create($data);
    }

    public function update(int $id, array $data)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($data);
        return $asset;
    }

    public function delete(int $id)
    {
        $asset = Asset::findOrFail($id);
        return $asset->delete();
    }

    public function allCategories()
    {
        return AssetCategory::withCount('assets')->orderBy('id', 'desc')->get();
    }

    public function allLocations()
    {
        return AssetLocation::with('branch')->orderBy('id', 'desc')->get();
    }

    public function allAssignments()
    {
        return AssetAssignment::with(['asset', 'employee'])->orderBy('id', 'desc')->get();
    }

    public function findAssignment(int $id)
    {
        return AssetAssignment::findOrFail($id);
    }

    public function createAssignment(array $data)
    {
        return AssetAssignment::create($data);
    }

    public function updateAssignment(int $id, array $data)
    {
        $assignment = AssetAssignment::findOrFail($id);
        $assignment->update($data);
        return $assignment;
    }
}
