<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQSystemInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Policies\HQPolicy;

class HQSystemController extends Controller
{
    public function index()
    {
        Gate::authorize('hq.viewDashboard');
        
        $systems = HQSystemInstance::with('tenant')->orderBy('last_seen_at', 'desc')->paginate(20);
        return view('admin.hq.systems.index', compact('systems'));
    }

    public function show($id)
    {
        Gate::authorize('hq.viewDashboard');
        
        $system = HQSystemInstance::with(['tenant', 'commands' => function($q) {
            $q->latest()->take(10);
        }, 'telemetry' => function($q) {
            $q->latest('received_at')->take(5);
        }])->findOrFail($id);
        
        // Also get some communication logs maybe
        $logs = \App\Models\HQCentralSyncLog::where('system_instance_id', $system->id)
            ->latest('created_at')
            ->take(5)
            ->get();
            
        return view('admin.hq.systems.show', compact('system', 'logs'));
    }
}
