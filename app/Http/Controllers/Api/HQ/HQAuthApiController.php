<?php

namespace App\Http\Controllers\Api\HQ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\IAM\HQApiKeyService;
use App\Domain\HQ\Services\IAM\SessionManagementService;
use App\Domain\HQ\Services\IAM\HQPermissionService;
use App\Domain\HQ\Services\IAM\HQServiceAccountService;
use App\Models\HQApiKey;
use Illuminate\Support\Facades\Auth;

class HQAuthApiController extends Controller
{
    public function __construct(
        private HQApiKeyService $apiKeyService,
        private SessionManagementService $sessionService,
        private HQPermissionService $permissionService,
        private HQServiceAccountService $serviceAccountService
    ) {}

    public function createApiKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $user = Auth::user(); // Assuming user is authenticated via some token middleware or session
        $tenantId = clone $request->input('tenant_id') ?? $user?->tenant_id;

        $plainToken = $this->apiKeyService->generateApiKey(
            user: $user,
            tenantId: $tenantId,
            name: $request->name,
            expiresAt: $request->expires_at ? new \DateTime($request->expires_at) : null
        );

        return response()->json([
            'message' => 'API Key created successfully. Save this token now as it will not be shown again.',
            'token' => $plainToken
        ]);
    }

    public function revokeApiKey(Request $request)
    {
        $request->validate([
            'api_key_id' => 'required|integer|exists:hq_api_keys,id',
        ]);

        $apiKey = HQApiKey::findOrFail($request->api_key_id);
        
        // Ensure user owns this key or has permission
        if (Auth::id() !== $apiKey->user_id && !$this->permissionService->hasRole(Auth::user(), 'super-admin')) {
            abort(403, 'Unauthorized to revoke this key.');
        }

        $this->apiKeyService->revokeApiKey($apiKey);

        return response()->json(['message' => 'API Key revoked.']);
    }

    public function getSessions(Request $request)
    {
        $sessions = $this->sessionService->getActiveSessions(Auth::user());
        return response()->json(['sessions' => $sessions]);
    }

    public function logout(Request $request)
    {
        $this->sessionService->forceLogoutUser(Auth::user());
        Auth::logout();
        return response()->json(['message' => 'Logged out successfully from all sessions.']);
    }

    public function getPermissions(Request $request)
    {
        $user = Auth::user();
        $permissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->slug;
            }
        }
        
        return response()->json(['permissions' => array_unique($permissions)]);
    }

    public function createServiceAccount(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|integer|exists:hq_tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (!$this->permissionService->hasRole(Auth::user(), 'super-admin')) {
            abort(403, 'Only super-admins can create service accounts via API.');
        }

        $result = $this->serviceAccountService->createServiceAccount(
            tenantId: $request->tenant_id,
            name: $request->name,
            description: $request->description
        );

        return response()->json([
            'message' => 'Service account created successfully.',
            'account' => $result['account'],
            'token' => $result['plain_token']
        ]);
    }
}
