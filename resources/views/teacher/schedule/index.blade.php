@extends('layouts.admin')

@section('title', 'Ders Programım')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Ders Programım</h2>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Haftalık Ders Programı</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Gün</th>
                            <th>Saat</th>
                            <th>Sınıf</th>
                            <th>Ders</th>
                            <th>Derslik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                        <tr>
                            <td><span class="badge badge-info">{{ $schedule->day_of_week }}</span></td>
                            <td>
                                {{ is_object($schedule->start_time) ? $schedule->start_time->format('H:i') : substr($schedule->start_time, 0, 5) }} - 
                                {{ is_object($schedule->end_time) ? $schedule->end_time->format('H:i') : substr($schedule->end_time, 0, 5) }}
                            </td>
                            <td>{{ $schedule->classroom->name ?? '-' }}</td>
                            <td>{{ $schedule->course->name ?? '-' }}</td>
                            <td>{{ $schedule->room ?? 'Varsayılan' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tanımlanmış bir ders programınız bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
