@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">My Goals</h2>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
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
                        @if($goals->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No goals set yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
