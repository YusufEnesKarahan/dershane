@extends('layouts.admin')

@section('title', 'Fleet Overview - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Fleet & Deployments</h2>
            <p class="text-muted mb-0">Monitor instance statuses, deployment orchestration, and rollout analytics.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.fleet.deployments') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-rocket fa-sm text-white-50 me-1"></i> New Deployment
            </a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Instances</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metrics['total_instances'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Online</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metrics['online'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Deploying</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metrics['deploying'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-spin fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">In Maintenance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metrics['maintenance'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Version Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Version Distribution</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($metrics['version_distribution'] ?? [] as $version => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            v{{ $version }}
                            <span class="badge bg-primary rounded-pill">{{ $count }} instances</span>
                        </li>
                        @empty
                        <li class="list-group-item px-0 text-muted">No instances found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fleet Modules</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.platform.hq_central.fleet.deployments') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-rocket me-2 text-primary"></i> Deployments</h6>
                                <small class="text-muted">Manage staged, canary, and rolling deployments.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.fleet.channels') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-code-branch me-2 text-success"></i> Release Channels</h6>
                                <small class="text-muted">Manage stable, beta, and nightly channels.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.fleet.groups') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-layer-group me-2 text-info"></i> Instance Groups</h6>
                                <small class="text-muted">Group instances for targeted deployments.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.fleet.maintenance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-tools me-2 text-warning"></i> Maintenance Windows</h6>
                                <small class="text-muted">Schedule and manage maintenance times.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
