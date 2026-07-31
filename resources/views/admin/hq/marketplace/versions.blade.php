@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2>Version History: {{ $extension->name }}</h2>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Release Notes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($extension->versions as $version)
                    <tr>
                        <td>{{ $version->version }}</td>
                        <td><span class="badge bg-info">{{ $version->status }}</span></td>
                        <td>{{ Str::limit($version->release_notes, 50) }}</td>
                        <td>{{ $version->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
