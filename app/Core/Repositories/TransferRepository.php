<?php

namespace App\Core\Repositories;

use App\Core\Repositories\Interfaces\TransferRepositoryInterface;
use App\Models\AssetTransfer;

class TransferRepository implements TransferRepositoryInterface
{
    public function all()
    {
        return AssetTransfer::with(['asset', 'fromLocation', 'toLocation'])->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return AssetTransfer::with(['asset', 'fromLocation', 'toLocation'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return AssetTransfer::create($data);
    }

    public function delete(int $id)
    {
        $transfer = AssetTransfer::findOrFail($id);
        return $transfer->delete();
    }
}
