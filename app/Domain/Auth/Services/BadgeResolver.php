<?php
namespace App\Domain\Auth\Services;

class BadgeResolver
{
    public function resolve(array $item): ?string
    {
        return $item['badge'] ?? null;
    }
}
