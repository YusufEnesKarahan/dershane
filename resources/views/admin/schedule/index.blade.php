@extends('layouts.admin')

@section('title', 'Ders Programı Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-slate-800">Ders Programı Yönetimi</h1>
        @can('schedule.create')
        <a href="{{ route('admin.schedule.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ders Programı Ekle
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.schedule.index') }}" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="classroom_id" class="sr-only">Sınıf</label>
                    <select name="classroom_id" id="classroom_id" class="form-control">
                        <option value="">-- Tüm Sınıflar --</option>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-3 mb-2">
                    <label for="teacher_id" class="sr-only">Öğretmen</label>
                    <select name="teacher_id" id="teacher_id" class="form-control">
                        <option value="">-- Tüm Öğretmenler --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary mb-2"><i class="fas fa-filter"></i> Filtrele</button>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ders Programı Listesi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Gün</th>
                            <th>Saat Dilimi</th>
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
                            <td>
                                <span class="badge badge-info">{{ $schedule->day_of_week }}</span>
                            </td>
                            <td>
                                {{ is_object($schedule->start_time) ? $schedule->start_time->format('H:i') : substr($schedule->start_time, 0, 5) }} - 
                                {{ is_object($schedule->end_time) ? $schedule->end_time->format('H:i') : substr($schedule->end_time, 0, 5) }}
                            </td>
                            <td>{{ $schedule->classroom->name ?? '-' }}</td>
                            <td>{{ $schedule->course->name ?? '-' }}</td>
                            <td>{{ $schedule->teacher ? $schedule->teacher->first_name . ' ' . $schedule->teacher->last_name : '-' }}</td>
                            <td>{{ $schedule->room ?? 'Varsayılan' }}</td>
                            <td>
                                @can('schedule.update')
                                <a href="{{ route('admin.schedule.edit', $schedule) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('schedule.delete')
                                <form action="{{ route('admin.schedule.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu ders programı kaydını silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Henüz ders programı kaydı bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
