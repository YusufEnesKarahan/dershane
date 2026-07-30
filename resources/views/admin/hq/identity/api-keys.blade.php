@extends('layouts.hq')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">API Keys</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Tenant</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Usage Count</th>
                            <th>Last Used</th>
                            <th>Expires At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keys as $key)
                        <tr>
                            <td>{{ $key->name }}</td>
                            <td>{{ $key->tenant->name ?? 'System' }}</td>
                            <td>{{ $key->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($key->is_revoked)
                                    <span class="badge badge-danger">Revoked</span>
                                @else
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                            <td>{{ $key->usage_count }}</td>
                            <td>{{ $key->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                            <td>{{ $key->expires_at?->format('Y-m-d H:i') ?? 'Never' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $keys->links() }}
        </div>
    </div>
</div>
@endsection
