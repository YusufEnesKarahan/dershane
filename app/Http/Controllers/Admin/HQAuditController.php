<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\HQ\Services\HQAuditService;
use App\Models\HQAuditLog;

class HQAuditController extends Controller
{
    public function __construct(
        protected HQAuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('hq.viewAuditLogs');

        $filters = $request->only([
            'action', 'category', 'severity', 'user_id', 
            'tenant_id', 'system_instance_id', 'date_start', 'date_end'
        ]);

        $logs = $this->auditService->getTimeline($filters, 50);

        return view('admin.hq.audit.index', compact('logs', 'filters'));
    }

    public function show($id)
    {
        $this->authorize('hq.viewAuditLogs');

        $log = HQAuditLog::with(['user', 'tenant', 'systemInstance'])->findOrFail($id);

        return view('admin.hq.audit.show', compact('log'));
    }
}
