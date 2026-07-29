<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQLicenseValidationService;

class HQLicenseValidationController extends Controller
{
    public function __construct(
        protected HQLicenseValidationService $validationService
    ) {}

    /**
     * POST /api/hq/license/validate
     *
     * Validate a remote ERP instance's license status.
     */
    public function validate(Request $request)
    {
        $payload = $request->json()->all();

        $systemUuid = $payload['system_uuid'] ?? null;
        $licenseKey = $payload['license_key'] ?? null;

        if (!$systemUuid) {
            return response()->json(['error' => 'Missing system_uuid'], 400);
        }

        $result = $this->validationService->validateSystemLicense($systemUuid, $licenseKey);

        $statusCode = $result['success'] ? 200 : 404;

        return response()->json($result, $statusCode);
    }
}
