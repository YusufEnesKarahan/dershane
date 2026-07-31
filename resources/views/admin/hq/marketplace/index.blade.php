@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>HQ Central Marketplace</h1>
            <p>Discover, install, and manage extensions for your tenant.</p>
            
            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('hq.marketplace.extensions') }}">Available Extensions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('hq.marketplace.installed') }}">Installed Extensions</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @yield('marketplace-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
