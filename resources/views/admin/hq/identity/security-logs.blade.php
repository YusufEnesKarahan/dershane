@extends('layouts.hq')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Security Logs</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Severity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->action }}</td>
                            <td>
                                @if($log->severity === 'danger' || $log->severity === 'critical')
                                    <span class="badge badge-danger">{{ $log->severity }}</span>
                                @elseif($log->severity === 'warning')
                                    <span class="badge badge-warning">{{ $log->severity }}</span>
                                @else
                                    <span class="badge badge-info">{{ $log->severity }}</span>
                                @endif
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
