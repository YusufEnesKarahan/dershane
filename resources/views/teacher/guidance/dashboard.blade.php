@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-slate-800">Teacher Guidance Dashboard</h2>
    </div>

    <div class="row">
        <!-- Needs Attention -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Students Needing Attention</h6>
                </div>
                <div class="card-body">
                    @if($needsAttention->isEmpty())
                        <p class="text-success">No students currently in High or Critical risk.</p>
                    @else
                        <ul class="list-group">
                        @foreach($needsAttention as $student)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $student->first_name }} {{ $student->last_name }}
                                <span class="badge badge-danger badge-pill">Review</span>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Meetings</h6>
                </div>
                <div class="card-body">
                    @if($upcomingMeetings->isEmpty())
                        <p class="text-muted">No upcoming meetings scheduled.</p>
                    @else
                        <ul class="list-group">
                        @foreach($upcomingMeetings as $meeting)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $meeting->student->first_name }} {{ $meeting->student->last_name }}</strong><br>
                                    <small>{{ $meeting->meeting_date->format('d M, Y H:i') }}</small>
                                </div>
                                <span class="badge badge-info badge-pill">{{ $meeting->meeting_type }}</span>
                            </li>
                        @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
