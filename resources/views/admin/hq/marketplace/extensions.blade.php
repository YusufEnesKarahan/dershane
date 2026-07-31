@extends('admin.hq.marketplace.index')

@section('marketplace-content')
    <h3>Available Extensions</h3>
    <div class="row">
        @foreach($extensions ?? [] as $extension)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $extension->name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">By {{ $extension->vendor }}</h6>
                        <p class="card-text">{{ Str::limit($extension->description, 100) }}</p>
                        <a href="{{ route('hq.marketplace.details', $extension->slug) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
        
        @if(empty($extensions))
            <div class="col-12">
                <p>No extensions available at the moment.</p>
            </div>
        @endif
    </div>
@endsection
