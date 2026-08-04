@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Student Meetings</h2>
        <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-calendar-plus fa-sm text-white-50"></i> Schedule Meeting
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Teacher</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $meeting)
                        <tr>
                            <td>{{ $meeting->meeting_date->format('d.m.Y H:i') }}</td>
                            <td>{{ $meeting->student->first_name }} {{ $meeting->student->last_name }}</td>
                            <td>{{ $meeting->teacher->first_name }} {{ $meeting->teacher->last_name }}</td>
                            <td>{{ $meeting->meeting_type }}</td>
                            <td>
                                <a href="{{ route('admin.meetings.show', $meeting->id) }}" class="btn btn-sm btn-info">Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $meetings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
