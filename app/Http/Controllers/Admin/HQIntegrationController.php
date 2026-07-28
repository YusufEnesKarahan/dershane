<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\HQIntegrationService;

class HQIntegrationController extends Controller
{
    public function __construct(protected HQIntegrationService $hqIntegrationService) {}

    public function index()
    {
        $identity = $this->hqIntegrationService->getInstanceInformation();
        $licenseStatus = $this->hqIntegrationService->getLicenseStatus();
        $enabledFeatures = $this->hqIntegrationService->getEnabledFeatures();
        $healthSummary = $this->hqIntegrationService->getHealthSummary();

        return view('admin.platform.hq_integration.index', compact(
            'identity',
            'licenseStatus',
            'enabledFeatures',
            'healthSummary'
        ));
    }
}
