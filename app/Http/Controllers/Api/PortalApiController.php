<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQTenant;
use App\Domain\Portal\Services\TenantDashboardService;
use App\Domain\Portal\Services\ApiKeyService;
use App\Domain\Portal\Services\SupportTicketService;
use App\Models\PortalApiKey;
use Exception;

class PortalApiController extends Controller
{
    protected $dashboardService;
    protected $apiKeyService;
    protected $ticketService;

    public function __construct(
        TenantDashboardService $dashboardService,
        ApiKeyService $apiKeyService,
        SupportTicketService $ticketService
    ) {
        $this->dashboardService = $dashboardService;
        $this->apiKeyService = $apiKeyService;
        $this->ticketService = $ticketService;
        
        // Ensure authentication and IAM permissions are checked via middleware
        $this->middleware('auth:sanctum');
        $this->middleware('hq.api.signature'); // Optional, if using HMAC
    }

    protected function getTenant(Request $request): HQTenant
    {
        // In a real scenario, this would be derived from the authenticated user's session
        $tenantId = $request->header('X-Tenant-ID') ?? $request->user()->tenant_id;
        return HQTenant::findOrFail($tenantId);
    }

    public function dashboard(Request $request)
    {
        $tenant = $this->getTenant($request);
        $data = $this->dashboardService->getDashboardData($tenant);
        
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function subscription(Request $request)
    {
        $tenant = $this->getTenant($request);
        $subscription = $tenant->subscriptions()->where('status', 'active')->with('plan')->first();
        
        return response()->json(['status' => 'success', 'data' => $subscription]);
    }

    public function extensions(Request $request)
    {
        $tenant = $this->getTenant($request);
        $extensions = \App\Models\HQExtensionInstallation::where('tenant_id', $tenant->id)
            ->with('extension', 'version')
            ->get();
            
        return response()->json(['status' => 'success', 'data' => $extensions]);
    }

    public function usage(Request $request)
    {
        $tenant = $this->getTenant($request);
        $usage = \App\Models\HQUsageRecord::where('tenant_id', $tenant->id)->get();
        
        return response()->json(['status' => 'success', 'data' => $usage]);
    }

    public function invoices(Request $request)
    {
        $tenant = $this->getTenant($request);
        $invoices = \App\Models\HQInvoice::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['status' => 'success', 'data' => $invoices]);
    }

    public function createApiKey(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant = $this->getTenant($request);
        $keyData = $this->apiKeyService->createKey($tenant, $request->name, $request->user()->id ?? null);
        
        return response()->json(['status' => 'success', 'data' => $keyData]);
    }

    public function revokeApiKey(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $apiKey = PortalApiKey::where('tenant_id', $tenant->id)->findOrFail($id);
        
        $this->apiKeyService->revokeKey($apiKey);
        
        return response()->json(['status' => 'success', 'message' => 'API Key revoked successfully']);
    }

    public function createSupportTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high,critical',
        ]);

        $tenant = $this->getTenant($request);
        $ticket = $this->ticketService->createTicket($tenant, $request->all(), $request->user()->id ?? null);
        
        return response()->json(['status' => 'success', 'data' => $ticket]);
    }
}
