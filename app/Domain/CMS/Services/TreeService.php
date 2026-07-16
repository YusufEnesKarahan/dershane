<?php
namespace App\Domain\CMS\Services;

use App\Models\Page;

class TreeService
{
    public function buildTree(): array
    {
        return Page::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }
}
