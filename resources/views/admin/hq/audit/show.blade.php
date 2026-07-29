@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-gray-800">Audit Log Details</h2>
            <p class="text-muted mb-0">View deep details for the specific audit trail event.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.audit.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Timeline
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Event Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Action</div>
                        <div class="col-sm-8 fw-bold">{{ $log->action }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Timestamp</div>
                        <div class="col-sm-8">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Description</div>
                        <div class="col-sm-8">{{ $log->description ?? 'No description provided.' }}</div>
                    </div>
                    <hr>
                    
                    @if($log->old_values || $log->new_values)
                        <h6 class="mb-3">Value Changes</h6>
                        <div class="row">
                            @if($log->old_values)
                                <div class="col-md-6">
                                    <div class="text-danger mb-2 fw-bold">Old Values</div>
                                    <pre class="bg-light p-3 rounded border" style="font-size: 0.85rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                            @if($log->new_values)
                                <div class="col-md-6">
                                    <div class="text-success mb-2 fw-bold">New Values</div>
                                    <pre class="bg-light p-3 rounded border" style="font-size: 0.85rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if($log->metadata)
                        <h6 class="mb-3 mt-4">Metadata</h6>
                        <pre class="bg-light p-3 rounded border" style="font-size: 0.85rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meta Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-0 rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Context & Target</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Category</span>
                            <span class="badge bg-secondary text-capitalize">{{ $log->category }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Severity</span>
                            @php
                                $badgeClass = match($log->severity) {
                                    'info' => 'bg-info',
                                    'warning' => 'bg-warning text-dark',
                                    'danger' => 'bg-danger',
                                    'critical' => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-capitalize">{{ $log->severity }}</span>
                        </li>
                        
                        <li class="list-group-item px-0 pt-4 border-bottom-0 pb-0">
                            <h6 class="text-muted text-uppercase mb-3" style="font-size:0.75rem;">Source Actor</h6>
                        </li>
                        <li class="list-group-item px-0">
                            <strong>User:</strong> {{ $log->user->name ?? 'System / External API' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}
                        </li>
                        <li class="list-group-item px-0 text-break">
                            <strong>User Agent:</strong> <small class="text-muted">{{ $log->user_agent ?? 'N/A' }}</small>
                        </li>

                        <li class="list-group-item px-0 pt-4 border-bottom-0 pb-0">
                            <h6 class="text-muted text-uppercase mb-3" style="font-size:0.75rem;">Target System</h6>
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Tenant:</strong> 
                            @if($log->tenant)
                                <a href="#">{{ $log->tenant->name }}</a>
                            @else
                                <span class="text-muted">Global</span>
                            @endif
                        </li>
                        <li class="list-group-item px-0">
                            <strong>System Instance:</strong> 
                            @if($log->systemInstance)
                                <a href="#">{{ $log->systemInstance->name }}</a>
                            @else
                                <span class="text-muted">Global</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
