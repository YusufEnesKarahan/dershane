@extends('layouts.admin')

@section('title', 'Deployment Details - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Deployment #{{ $deployment->id }}</h2>
            <p class="text-muted mb-0">Version {{ $deployment->version }} • {{ ucfirst($deployment->type) }} Rollout</p>
        </div>
        <a href="{{ route('admin.platform.hq_central.fleet.deployments') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Target Statuses</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Error Message</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deployment->targets as $target)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ class_basename($target->targetable_type) }}</div>
                                        <small class="text-muted">ID: {{ $target->targetable_id }}</small>
                                    </td>
                                    <td>
                                        @if($target->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($target->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($target->status === 'rolled_back')
                                            <span class="badge bg-warning text-dark">Rolled Back</span>
                                        @elseif($target->status === 'running')
                                            <span class="badge bg-primary">Running</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-danger small">{{ $target->error_message ?? '-' }}</td>
                                    <td>{{ $target->updated_at->format('M d, H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Logs -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Deployment Logs</h6>
                </div>
                <div class="card-body bg-dark text-light font-monospace" style="max-height: 400px; overflow-y: auto;">
                    @forelse($deployment->logs as $log)
                        <div class="mb-1">
                            <span class="text-muted">[{{ $log->created_at->format('Y-m-d H:i:s') }}]</span>
                            @if($log->level === 'error')
                                <span class="text-danger">[ERROR]</span>
                            @elseif($log->level === 'warning')
                                <span class="text-warning">[WARN]</span>
                            @else
                                <span class="text-info">[INFO]</span>
                            @endif
                            {{ $log->message }}
                        </div>
                    @empty
                        <div class="text-muted">No logs available for this deployment.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overview</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="fw-bold">{{ ucfirst($deployment->status) }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Progress</span>
                            <span class="fw-bold">{{ $deployment->rollout_percentage }}%</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Started At</span>
                            <span class="fw-bold">{{ $deployment->started_at ? $deployment->started_at->format('Y-m-d H:i') : '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Completed At</span>
                            <span class="fw-bold">{{ $deployment->completed_at ? $deployment->completed_at->format('Y-m-d H:i') : '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Created By</span>
                            <span class="fw-bold">{{ $deployment->creator->name ?? 'System' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
