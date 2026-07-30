@extends('layouts.admin')

@section('title', 'Backup Snapshots - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Backup Snapshots</h2>
            <p class="text-muted mb-0">Browse point-in-time snapshots available for restoration.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Instance</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Created At</th>
                            <th>Expires At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshots as $snapshot)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $snapshot->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $snapshot->job->systemInstance->name ?? 'Unknown Instance' }}</div>
                                <small class="text-muted">Job #{{ $snapshot->job->id ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ ucfirst($snapshot->type) }}</span>
                            </td>
                            <td>{{ number_format($snapshot->size_bytes / 1048576, 2) }} MB</td>
                            <td>{{ $snapshot->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($snapshot->expires_at)
                                    @if($snapshot->expires_at->isPast())
                                        <span class="text-danger">Expired</span>
                                    @else
                                        {{ $snapshot->expires_at->diffForHumans() }}
                                    @endif
                                @else
                                    <span class="text-success">Never</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-warning">Restore</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No snapshots found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($snapshots->hasPages())
            <div class="card-footer">
                {{ $snapshots->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
