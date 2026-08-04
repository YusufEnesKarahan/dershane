@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">Student Goals</h2>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#createGoalModal">
            <i class="fas fa-bullseye fa-sm text-white-50"></i> Set Goal
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Goal</th>
                            <th>Target</th>
                            <th>Current</th>
                            <th>Deadline</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goals as $goal)
                        <tr>
                            <td>{{ $goal->student->first_name }} {{ $goal->student->last_name }}</td>
                            <td>{{ $goal->title }}</td>
                            <td>{{ $goal->target_value }}</td>
                            <td>{{ $goal->current_value }}</td>
                            <td>{{ $goal->deadline ? $goal->deadline->format('d.m.Y') : 'No Deadline' }}</td>
                            <td>
                                <span class="badge badge-{{ $goal->status == 'Achieved' ? 'success' : ($goal->status == 'Failed' ? 'danger' : 'info') }}">
                                    {{ $goal->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $goals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
