@extends('layouts.hq')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Active Sessions</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Last Activity</th>
                            <th>Expires At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->user->name ?? 'N/A' }}</td>
                            <td>{{ $session->ip_address }}</td>
                            <td>{{ Str::limit($session->user_agent, 50) }}</td>
                            <td>{{ $session->last_activity?->diffForHumans() }}</td>
                            <td>{{ $session->expires_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection
