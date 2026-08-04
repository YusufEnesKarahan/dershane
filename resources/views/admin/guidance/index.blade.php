@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Guidance Records</h2>
        <a href="{{ route('admin.guidance.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create Record
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Teacher</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                        <tr>
                            <td>{{ $record->student->first_name }} {{ $record->student->last_name }}</td>
                            <td>{{ $record->teacher->first_name }} {{ $record->teacher->last_name }}</td>
                            <td>{{ $record->category }}</td>
                            <td>
                                <span class="badge badge-{{ $record->priority == 'Critical' ? 'danger' : ($record->priority == 'High' ? 'warning' : 'info') }}">
                                    {{ $record->priority }}
                                </span>
                            </td>
                            <td>{{ $record->status }}</td>
                            <td>
                                <a href="{{ route('admin.guidance.show', $record->id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.guidance.edit', $record->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
