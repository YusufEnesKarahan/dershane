@extends('layouts.admin')

@section('title', 'Backup Jobs - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Backup Jobs</h2>
            <p class="text-muted mb-0">View execution history of automated and manual backup jobs.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Policy</th>
                            <th>Instance</th>
                            <th>Status</th>
                            <th>Size</th>
                            <th>Time</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $job->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $job->policy->name ?? 'Manual/Deleted' }}</div>
                            </td>
                            <td>{{ $job->systemInstance->name ?? '-' }}</td>
                            <td>
                                @if($job->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($job->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @elseif($job->status === 'running')
                                    <span class="badge bg-primary">Running</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                                
                                @if($job->error_message)
                                    <div class="small text-danger mt-1">{{ Str::limit($job->error_message, 30) }}</div>
                                @endif
                            </td>
                            <td>{{ number_format($job->size / 1048576, 2) }} MB</td>
                            <td>
                                <div class="small text-muted">
                                    Started: {{ $job->started_at ? $job->started_at->format('M d, H:i') : '-' }}<br>
                                    Ended: {{ $job->finished_at ? $job->finished_at->format('M d, H:i') : '-' }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.platform.hq_central.backups.jobs.show', $job->id) ?? '#' }}" class="btn btn-sm btn-outline-primary">Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No backup jobs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jobs->hasPages())
            <div class="card-footer">
                {{ $jobs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
