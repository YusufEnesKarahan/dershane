@extends('layouts.admin')

@section('title', 'Storage Locations - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Storage Locations</h2>
            <p class="text-muted mb-0">Manage S3, Local, MinIO, and FTP storage backends for backups.</p>
        </div>
        <button class="btn btn-secondary shadow-sm"><i class="fas fa-plus me-1"></i> Add Storage</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Capacity / Used</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                        <tr>
                            <td class="ps-4 font-weight-bold">{{ $location->name }}</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($location->driver) }}</span></td>
                            <td>
                                @if($location->is_active)
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-times-circle"></i> Inactive</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $usedMB = number_format($location->used_bytes / 1048576, 2);
                                    $capacityMB = $location->capacity_bytes ? number_format($location->capacity_bytes / 1048576, 2) . ' MB' : 'Unlimited';
                                @endphp
                                {{ $usedMB }} MB / {{ $capacityMB }}
                                @if($location->capacity_bytes)
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-info" style="width: {{ min(100, ($location->used_bytes / $location->capacity_bytes) * 100) }}%"></div>
                                </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No storage locations configured.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($locations->hasPages())
            <div class="card-footer">
                {{ $locations->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
