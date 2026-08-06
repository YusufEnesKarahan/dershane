@extends('layouts.admin')

@section('title', 'Ders Programı Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-slate-800">Ders Programları</h1>
        <div>
            @can('schedules.create')
                <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Ders Programı
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tüm Ders Programları</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Gün</th>
                            <th>Saat</th>
                            <th>Sınıf</th>
                            <th>Ders</th>
                            <th>Öğretmen</th>
                            <th>Derslik</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ __('days.'.$schedule->day_of_week) ?? $schedule->day_of_week }}</td>
                                <td>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</td>
                                <td>{{ $schedule->classroom->name ?? '-' }}</td>
                                <td>{{ $schedule->course->name ?? '-' }}</td>
                                <td>{{ $schedule->teacher->user->name ?? '-' }}</td>
                                <td>{{ $schedule->room ?? '-' }}</td>
                                <td>
                                    @can('schedules.update')
                                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-info">Düzenle</a>
                                    @endcan
                                    @can('schedules.delete')
                                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Emin misiniz?')">Sil</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Kayıt bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
