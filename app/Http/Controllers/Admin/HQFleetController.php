<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HQDeployment;
use App\Models\HQReleaseChannel;
use App\Models\HQInstanceGroup;
use App\Models\HQMaintenanceWindow;
use App\Domain\HQ\Services\Fleet\FleetService;

class HQFleetController extends Controller
{
    protected FleetService $fleetService;

    public function __construct(FleetService $fleetService)
    {
        $this->fleetService = $fleetService;
    }

    public function overview()
    {
        $metrics = $this->fleetService->getFleetOverview();
        return view('admin.hq.fleet.overview', compact('metrics'));
    }

    public function deployments()
    {
        $deployments = HQDeployment::with('creator')->latest()->paginate(20);
        return view('admin.hq.fleet.deployments', compact('deployments'));
    }

    public function deploymentShow(HQDeployment $deployment)
    {
        $deployment->load(['targets.targetable', 'logs.systemInstance', 'creator']);
        return view('admin.hq.fleet.deployment_show', compact('deployment'));
    }

    public function channels()
    {
        $channels = HQReleaseChannel::withCount('tenants')->get();
        return view('admin.hq.fleet.channels', compact('channels'));
    }

    public function groups()
    {
        $groups = HQInstanceGroup::withCount('tenants')->get();
        return view('admin.hq.fleet.groups', compact('groups'));
    }

    public function maintenance()
    {
        $windows = HQMaintenanceWindow::with('targetable')->latest()->paginate(20);
        return view('admin.hq.fleet.maintenance', compact('windows'));
    }
}
