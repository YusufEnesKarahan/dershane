@extends('layouts.admin')

@section('title', 'Maintenance Windows - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Maintenance Windows</h2>
            <p class="text-muted mb-0">Schedule downtime and maintenance modes for instances and groups.</p>
        </div>
        <button class="btn btn-primary"><i class="fas fa-calendar-plus me-1"></i> Schedule Window</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Target</th>
                            <th>Status</th>
                            <th>Time Window</th>
                            <th>Reason</th>
                            <th>Auto Manage</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($windows as $window)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ class_basename($window->targetable_type) }}</div>
                                <small class="text-muted">{{ $window->targetable->name ?? 'Unknown Target' }}</small>
                            </td>
                            <td>
                                @if($window->status === 'active')
                                    <span class="badge bg-danger"><i class="fas fa-tools"></i> Active</span>
                                @elseif($window->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($window->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    <div><strong>Start:</strong> {{ $window->starts_at->format('M d, H:i') }}</div>
                                    <div><strong>End:</strong> {{ $window->ends_at->format('M d, H:i') }}</div>
                                </div>
                            </td>
                            <td class="text-muted">{{ Str::limit($window->reason, 40) }}</td>
                            <td>
                                @if($window->auto_manage)
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Yes</span>
                                @else
                                    <span class="text-secondary"><i class="fas fa-times-circle"></i> No</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($window->status === 'scheduled')
                                <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled>Locked</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No maintenance windows found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
