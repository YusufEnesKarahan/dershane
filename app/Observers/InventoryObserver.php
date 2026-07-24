<?php

namespace App\Observers;

use App\Models\InventoryItem;
use Illuminate\Support\Str;

class InventoryObserver
{
    public function creating(InventoryItem $item): void
    {
        if (empty($item->sku)) {
            $item->sku = 'SKU-' . strtoupper(Str::random(8));
        }
    }
}
