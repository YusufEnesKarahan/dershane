@extends('layouts.admin')

@section('title', 'Backup Policies - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Backup Policies</h2>
            <p class="text-muted mb-0">Define automated scheduling and storage routines.</p>
        </div>
        <button class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> New Policy</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Policy Name</th>
                            <th>Target</th>
                            <th>Frequency</th>
                            <th>Storage</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($policies as $policy)
                        <tr>
                            <td class="ps-4 font-weight-bold">{{ $policy->name }}</td>
                            <td>
                                @if($policy->tenant)
                                    <span class="badge bg-primary">Tenant: {{ $policy->tenant->name }}</span>
                                @elseif($policy->systemInstance)
                                    <span class="badge bg-info text-dark">Instance: {{ $policy->systemInstance->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Global</span>
                                @endif
                            </td>
                            <td><span class="text-capitalize">{{ $policy->frequency }}</span></td>
                            <td>{{ $policy->storageLocation->name ?? 'Default' }}</td>
                            <td>
                                @if($policy->is_active)
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Active</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-times-circle"></i> Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No backup policies found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($policies->hasPages())
            <div class="card-footer">
                {{ $policies->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
