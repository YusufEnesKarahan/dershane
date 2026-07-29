<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQCentralCommand;
use App\Models\HQTenant;
use App\Models\HQSystemInstance;
use App\Domain\HQ\Enums\HQCommandType;
use App\Domain\HQ\Services\HQRemoteCommandService;
use Illuminate\Support\Facades\Gate;

class HQRemoteCommandController extends Controller
{
    public function __construct(
        protected HQRemoteCommandService $remoteCommandService
    ) {}

    public function index()
    {
        Gate::authorize('hq.viewDashboard');

        $commands = HQCentralCommand::with('instance.tenant')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $stats = [
            'pending' => HQCentralCommand::where('status', 'pending')->count(),
            'failed' => HQCentralCommand::where('status', 'failed')->count(),
            'completed' => HQCentralCommand::where('status', 'completed')->count(),
        ];

        return view('admin.hq.commands.index', compact('commands', 'stats'));
    }

    public function show(HQCentralCommand $command)
    {
        Gate::authorize('hq.viewDashboard');

        $command->load('instance.tenant');

        return view('admin.hq.commands.show', compact('command'));
    }

    public function create()
    {
        Gate::authorize('hq.manageSystem');

        $tenants = HQTenant::active()->get();
        $instances = HQSystemInstance::online()->get();
        $commandTypes = HQCommandType::cases();

        return view('admin.hq.commands.create', compact('tenants', 'instances', 'commandTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('hq.manageSystem');

        $validated = $request->validate([
            'target_type' => 'required|in:instance,tenant,all',
            'target_id' => 'required_unless:target_type,all',
            'command_type' => 'required|string',
            'priority' => 'nullable|integer',
            'payload_json' => 'nullable|string',
        ]);

        $type = HQCommandType::tryFrom($validated['command_type']);
        if (!$type) {
            return back()->with('error', 'Invalid command type.');
        }

        $priority = $validated['priority'] ?? 0;
        
        $payload = [];
        if (!empty($validated['payload_json'])) {
            $payload = json_decode($validated['payload_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid JSON payload.');
            }
        }

        try {
            if ($validated['target_type'] === 'instance') {
                $instance = HQSystemInstance::findOrFail($validated['target_id']);
                $this->remoteCommandService->dispatchCommand($instance, $type, $payload, $priority);
            } elseif ($validated['target_type'] === 'tenant') {
                $tenant = HQTenant::findOrFail($validated['target_id']);
                $this->remoteCommandService->dispatchToTenant($tenant, $type, $payload, $priority);
            } else {
                $this->remoteCommandService->dispatchToAll($type, $payload, $priority);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to dispatch command: ' . $e->getMessage());
        }

        return redirect()->route('admin.hq.commands.index')->with('success', 'Command(s) dispatched successfully.');
    }
}
