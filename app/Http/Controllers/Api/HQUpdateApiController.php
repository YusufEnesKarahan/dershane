<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQUpdateService;
use App\Domain\HQ\Services\HQVersionService;

class HQUpdateApiController extends Controller
{
    public function __construct(
        protected HQUpdateService $updateService,
        protected HQVersionService $versionService
    ) {}

    public function check(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string',
            'current_version' => 'required|string',
        ]);

        $latest = $this->versionService->getLatestStableVersion();
        $isMandatory = $this->versionService->checkIfMandatoryUpdateRequired($validated['current_version']);

        return response()->json([
            'latest_version' => $latest ? $latest->version : null,
            'needs_update' => $latest && version_compare($validated['current_version'], $latest->version, '<'),
            'is_mandatory' => $isMandatory,
            'release_notes' => $latest ? $latest->release_notes : null,
        ]);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string',
            'job_id' => 'required|integer',
        ]);

        $this->updateService->recordProgress($validated['job_id'], 0);

        return response()->json(['status' => 'acknowledged']);
    }

    public function progress(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string',
            'job_id' => 'required|integer',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $this->updateService->recordProgress($validated['job_id'], $validated['progress']);

        return response()->json(['status' => 'recorded']);
    }

    public function finished(Request $request)
    {
        $validated = $request->validate([
            'system_uuid' => 'required|string',
            'job_id' => 'required|integer',
            'success' => 'required|boolean',
            'message' => 'nullable|string',
            'result' => 'nullable|array',
        ]);

        $this->updateService->recordFinished(
            $validated['job_id'],
            $validated['success'],
            $validated['message'] ?? null,
            $validated['result'] ?? null
        );

        return response()->json(['status' => 'finished']);
    }
}
