<?php

namespace App\Domain\Reporting\Actions;

use App\Domain\Reporting\Services\ExecutiveDashboardService;
use App\Models\DashboardSnapshot;

class GenerateDashboardSnapshot
{
    public function __construct(protected ExecutiveDashboardService $service) {}

    public function execute(): DashboardSnapshot
    {
        return $this->service->generateSnapshot();
    }
}
