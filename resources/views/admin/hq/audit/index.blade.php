@extends('layouts.admin')

@section('title', 'HQ Enterprise Audit Trail')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-gray-800">HQ Enterprise Audit Trail</h2>
            <p class="text-muted mb-0">Centralized log of all critical operations across tenants, licenses, and systems.</p>
        </div>
        <div>
            <a href="{{ route('admin.platform.hq_central.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to HQ Central
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.platform.hq_central.audit.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="system" {{ request('category') == 'system' ? 'selected' : '' }}>System</option>
                        <option value="user" {{ request('category') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="security" {{ request('category') == 'security' ? 'selected' : '' }}>Security</option>
                        <option value="license" {{ request('category') == 'license' ? 'selected' : '' }}>License</option>
                        <option value="command" {{ request('category') == 'command' ? 'selected' : '' }}>Command</option>
                        <option value="update" {{ request('category') == 'update' ? 'selected' : '' }}>Update</option>
                        <option value="configuration" {{ request('category') == 'configuration' ? 'selected' : '' }}>Configuration</option>
                        <option value="backup" {{ request('category') == 'backup' ? 'selected' : '' }}>Backup</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Severity</label>
                    <select name="severity" class="form-select">
                        <option value="">All Severities</option>
                        <option value="info" {{ request('severity') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="danger" {{ request('severity') == 'danger' ? 'selected' : '' }}>Danger</option>
                        <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action</label>
                    <input type="text" name="action" class="form-control" value="{{ request('action') }}" placeholder="e.g. backup.started">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(count(request()->all()) > 0)
                        <a href="{{ route('admin.platform.hq_central.audit.index') }}" class="btn btn-light ms-2">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Severity</th>
                            <th class="py-3">Action</th>
                            <th class="py-3">Target</th>
                            <th class="py-3">User/Source</th>
                            <th class="px-4 py-3 text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-nowrap text-muted">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-secondary text-capitalize">{{ $log->category }}</span>
                                </td>
                                <td class="py-3">
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
                                </td>
                                <td class="py-3 fw-bold">
                                    {{ $log->action }}
                                </td>
                                <td class="py-3">
                                    @if($log->tenant)
                                        <div><small class="text-muted">Tenant:</small> {{ $log->tenant->name }}</div>
                                    @endif
                                    @if($log->systemInstance)
                                        <div><small class="text-muted">System:</small> {{ $log->systemInstance->name }}</div>
                                    @endif
                                    @if(!$log->tenant && !$log->systemInstance)
                                        <span class="text-muted">Global / System</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($log->user)
                                        {{ $log->user->name }}
                                    @else
                                        System ({{ $log->ip_address ?? 'internal' }})
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.platform.hq_central.audit.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x mb-3 text-light"></i>
                                    <h5>No audit logs found.</h5>
                                    <p>Try adjusting your filters or search criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top px-4 py-3">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
