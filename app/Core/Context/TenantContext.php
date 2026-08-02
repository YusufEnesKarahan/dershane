<?php

namespace App\Core\Context;

class TenantContext
{
    protected static ?int $activeBranchId = null;

    public static function setActiveBranchId(?int $branchId): void
    {
        self::$activeBranchId = $branchId;
    }

    public static function getActiveBranchId(): ?int
    {
        return self::$activeBranchId;
    }

    public static function clear(): void
    {
        self::$activeBranchId = null;
    }
}
