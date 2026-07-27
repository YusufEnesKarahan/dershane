<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(protected LicenseService $licenseService) {}

    public function index()
    {
        $licenseStatus = $this->licenseService->checkLicense();
        $license = $this->licenseService->getCurrentLicense();

        return view('admin.platform.licenses.index', compact('licenseStatus', 'license'));
    }
}
