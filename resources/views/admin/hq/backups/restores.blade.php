@extends('layouts.admin')

@section('title', 'Restore Jobs - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Restore Jobs</h2>
            <p class="text-muted mb-0">Monitor dry-runs and active database/file restoration tasks.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Target Instance</th>
                            <th>Snapshot</th>
                            <th>Mode</th>
                            <th>Status</th>
                            <th>Time</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($restores as $restore)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $restore->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $restore->targetInstance->name ?? 'Unknown Instance' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">Snap #{{ $restore->hq_backup_snapshot_id }}</span>
                            </td>
                            <td>
                                @if($restore->mode === 'dry_run')
                                    <span class="badge bg-info text-dark"><i class="fas fa-vial"></i> Dry Run</span>
                                @elseif($restore->mode === 'validation')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-check-double"></i> Validation</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-bolt"></i> Execute</span>
                                @endif
                            </td>
                            <td>
                                @if($restore->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($restore->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @elseif($restore->status === 'running')
                                    <span class="badge bg-primary">Running</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                                
                                @if($restore->error_message)
                                    <div class="small text-danger mt-1">{{ Str::limit($restore->error_message, 30) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted">
                                    Started: {{ $restore->started_at ? $restore->started_at->format('M d, H:i') : '-' }}<br>
                                    Ended: {{ $restore->completed_at ? $restore->completed_at->format('M d, H:i') : '-' }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary">Details</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No restore jobs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($restores->hasPages())
            <div class="card-footer">
                {{ $restores->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
