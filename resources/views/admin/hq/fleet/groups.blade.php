@extends('layouts.admin')

@section('title', 'Instance Groups - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Instance Groups</h2>
            <p class="text-muted mb-0">Organize instances for targeted deployments or regional management.</p>
        </div>
        <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> New Group</button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Group Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Tenants</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td class="ps-4 font-weight-bold">{{ $group->name }}</td>
                            <td><code>{{ $group->slug }}</code></td>
                            <td class="text-muted">{{ Str::limit($group->description, 50) }}</td>
                            <td>
                                <span class="badge bg-primary rounded-pill">{{ $group->tenants_count }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No instance groups found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
