@extends('layouts.admin')

@section('title', 'Release Channels - HQ Central')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 text-gray-800 mb-0">Release Channels</h2>
            <p class="text-muted mb-0">Manage distribution tracks for tenants and instances.</p>
        </div>
        <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> New Channel</button>
    </div>

    <div class="row">
        @forelse($channels as $channel)
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-left-{{ $channel->is_default ? 'success' : 'primary' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title font-weight-bold text-gray-800 mb-0">{{ $channel->name }}</h5>
                        @if($channel->is_default)
                        <span class="badge bg-success">Default</span>
                        @endif
                    </div>
                    <p class="text-muted small mb-4">{{ $channel->description ?? 'No description provided.' }}</p>
                    
                    <div class="d-flex justify-content-between align-items-end mt-auto">
                        <div>
                            <div class="text-xs font-weight-bold text-uppercase text-muted">Assigned Tenants</div>
                            <div class="h4 mb-0 text-gray-800">{{ $channel->tenants_count }}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Manage</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3">No release channels configured.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
