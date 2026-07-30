@extends('layouts.admin')

@section('title', 'Disaster Recovery Plans - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Disaster Recovery Plans</h2>
            <p class="text-muted mb-0">Manage and test DR runbooks for failover orchestration.</p>
        </div>
        <button class="btn btn-danger shadow-sm"><i class="fas fa-plus me-1"></i> New DR Plan</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Plan Name</th>
                            <th>Tenant</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Last Run</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                        <tr>
                            <td class="ps-4 font-weight-bold">{{ $plan->name }}</td>
                            <td>{{ $plan->tenant->name ?? '-' }}</td>
                            <td>
                                @if($plan->priority === 'high')
                                    <span class="badge bg-danger">High</span>
                                @elseif($plan->priority === 'medium')
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @else
                                    <span class="badge bg-secondary">Low</span>
                                @endif
                            </td>
                            <td>
                                @if($plan->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($plan->status === 'testing')
                                    <span class="badge bg-info">Testing</span>
                                @elseif($plan->status === 'running')
                                    <span class="badge bg-primary">Running</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($plan->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $plan->last_run_at ? $plan->last_run_at->format('M d, Y H:i') : 'Never' }}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-danger">Execute Runbook</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No DR plans configured.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($plans->hasPages())
            <div class="card-footer">
                {{ $plans->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
