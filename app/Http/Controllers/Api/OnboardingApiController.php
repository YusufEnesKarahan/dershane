<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Onboarding\Services\OnboardingService;
use App\Domain\Onboarding\Services\InvitationService;
use App\Models\HQOnboardingFlow;
use App\Models\HQTenant;
use App\Models\Role;

class OnboardingApiController extends Controller
{
    protected $onboardingService;
    protected $invitationService;

    public function __construct(OnboardingService $onboardingService, InvitationService $invitationService)
    {
        $this->onboardingService = $onboardingService;
        $this->invitationService = $invitationService;
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // other initial data...
        ]);

        $flow = $this->onboardingService->startOnboarding($validated);

        return response()->json([
            'message' => 'Onboarding started successfully',
            'flow_id' => $flow->uuid,
        ], 201);
    }

    public function step(Request $request)
    {
        $validated = $request->validate([
            'flow_id' => 'required|uuid|exists:hq_onboarding_flows,uuid',
            'step' => 'required|string',
            'payload' => 'nullable|array',
        ]);

        $flow = HQOnboardingFlow::where('uuid', $validated['flow_id'])->firstOrFail();

        try {
            $updatedFlow = $this->onboardingService->advanceStep($flow, $validated['step'], $validated['payload'] ?? []);
            
            return response()->json([
                'message' => "Step {$validated['step']} completed successfully",
                'flow' => $updatedFlow,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error advancing step',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'flow_id' => 'required|uuid|exists:hq_onboarding_flows,uuid',
        ]);

        $flow = HQOnboardingFlow::where('uuid', $validated['flow_id'])->firstOrFail();
        
        $this->onboardingService->completeOnboarding($flow);

        return response()->json([
            'message' => 'Onboarding completion queued',
        ]);
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:hq_tenants,id',
            'email' => 'required|email',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $tenant = HQTenant::findOrFail($validated['tenant_id']);
        $role = isset($validated['role_id']) ? Role::find($validated['role_id']) : null;

        $invitation = $this->invitationService->inviteUser($tenant, $validated['email'], $role);

        return response()->json([
            'message' => 'Invitation sent successfully',
            'invitation_id' => $invitation->uuid,
        ], 201);
    }

    public function status(Request $request)
    {
        $validated = $request->validate([
            'flow_id' => 'required|uuid|exists:hq_onboarding_flows,uuid',
        ]);

        $flow = HQOnboardingFlow::where('uuid', $validated['flow_id'])->firstOrFail();

        return response()->json([
            'status' => $flow->status,
            'current_step' => $flow->current_step,
            'metadata' => $flow->metadata,
        ]);
    }
}
