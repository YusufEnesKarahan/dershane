@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ $extension->name }}</h3>
                    <span class="badge bg-secondary">{{ $extension->type }}</span>
                </div>
                <div class="card-body">
                    <h5>Vendor: {{ $extension->vendor }}</h5>
                    <p class="lead">{{ $extension->description }}</p>
                    <hr>
                    <h4>Latest Version: {{ $extension->versions->first()->version ?? 'Unknown' }}</h4>
                    
                    @if($extension->is_compatible ?? true)
                        <div class="alert alert-success">Compatible with your system.</div>
                    @else
                        <div class="alert alert-danger">
                            Incompatible: <br>
                            <ul>
                                @foreach($extension->compatibility_issues ?? [] as $issue)
                                    <li>{{ $issue }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <a href="{{ route('hq.marketplace.versions', $extension->slug) }}" class="btn btn-outline-secondary">View Version History</a>
                </div>
                <div class="card-footer">
                    @if(isset($installation))
                        @if($installation->status === 'activated')
                            <button class="btn btn-warning">Disable Extension</button>
                        @else
                            <button class="btn btn-success">Enable Extension</button>
                        @endif
                        <button class="btn btn-danger float-end">Uninstall</button>
                    @else
                        <button class="btn btn-primary" {{ !($extension->is_compatible ?? true) ? 'disabled' : '' }}>Install Now</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
