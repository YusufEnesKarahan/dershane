@extends('admin.hq.marketplace.index')

@section('marketplace-content')
    <h3>Installed Extensions</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Extension</th>
                <th>Version</th>
                <th>Status</th>
                <th>Enabled At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($installations ?? [] as $install)
                <tr>
                    <td>{{ $install->extension->name }}</td>
                    <td>{{ $install->version->version }}</td>
                    <td>
                        <span class="badge {{ $install->status === 'activated' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($install->status) }}
                        </span>
                    </td>
                    <td>{{ $install->enabled_at ? $install->enabled_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('hq.marketplace.details', $install->extension->slug) }}" class="btn btn-sm btn-info">Manage</a>
                        <!-- Forms for Enable/Disable/Uninstall would go here via API endpoints -->
                    </td>
                </tr>
            @endforeach
            
            @if(empty($installations))
                <tr>
                    <td colspan="5" class="text-center">No extensions installed.</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
