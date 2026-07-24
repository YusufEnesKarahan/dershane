<?php

namespace App\Observers;

use App\Models\Asset;
use Illuminate\Support\Str;

class AssetObserver
{
    public function creating(Asset $asset): void
    {
        if (empty($asset->asset_code)) {
            $asset->asset_code = 'AST-' . strtoupper(Str::random(8));
        }
    }
}
