@extends('layouts.admin')

@section('title', 'Backup & DR Overview - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Enterprise Backup & Disaster Recovery</h2>
            <p class="text-muted mb-0">Monitor backup health, manage policies, and track disaster recovery readiness.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.backups.policies') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-shield-alt fa-sm text-white-50 me-1"></i> Manage Policies
            </a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Successful Backups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['successful_backups'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed Backups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['failed_backups'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Storage Usage</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(($stats['storage_usage'] ?? 0) / 1048576, 2) }} MB</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Restore Success</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['restore_success'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Links -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modules</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.platform.hq_central.backups.policies') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-clipboard-list me-2 text-primary"></i> Backup Policies</h6>
                                <small class="text-muted">Manage automated backup schedules and retention rules.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.backups.jobs') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-tasks me-2 text-info"></i> Backup Jobs</h6>
                                <small class="text-muted">View history of successful and failed backup runs.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.backups.snapshots') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-camera me-2 text-success"></i> Snapshots</h6>
                                <small class="text-muted">Browse available point-in-time and full snapshots.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.backups.restores') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-undo me-2 text-warning"></i> Restore Jobs</h6>
                                <small class="text-muted">Monitor dry-runs and actual restoration processes.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.backups.storage') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-hdd me-2 text-secondary"></i> Storage Locations</h6>
                                <small class="text-muted">Manage S3, Local, MinIO, and FTP storage backends.</small>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                        <a href="{{ route('admin.platform.hq_central.backups.dr_plans') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-fire-extinguisher me-2 text-danger"></i> DR Plans</h6>
                                <small class="text-muted">Configure and test Disaster Recovery runbooks.</small>
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
