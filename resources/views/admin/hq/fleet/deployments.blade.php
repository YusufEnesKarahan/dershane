@extends('layouts.admin')

@section('title', 'Deployments - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Deployments</h2>
            <p class="text-muted mb-0">Manage and monitor orchestration tasks across the fleet.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Deployment History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Version</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Started</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deployments as $deployment)
                        <tr>
                            <td>#{{ $deployment->id }}</td>
                            <td><span class="badge bg-secondary">v{{ $deployment->version }}</span></td>
                            <td><span class="badge bg-info">{{ ucfirst($deployment->type) }}</span></td>
                            <td>
                                @if($deployment->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($deployment->status === 'running')
                                    <span class="badge bg-primary">Running</span>
                                @elseif($deployment->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @elseif($deployment->status === 'rollback')
                                    <span class="badge bg-warning text-dark">Rollback</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($deployment->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $deployment->rollout_percentage }}%</span>
                                    <div class="progress flex-grow-1" style="height: 5px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $deployment->rollout_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $deployment->started_at ? $deployment->started_at->format('M d, Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.platform.hq_central.fleet.deployments.show', $deployment) }}" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No deployments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($deployments->hasPages())
            <div class="mt-4">
                {{ $deployments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
