@extends('layouts.admin')

@section('title', 'Ders Programı Takvimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-slate-800">Ders Programı Takvimi</h1>
        <div>
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
                <i class="fas fa-list"></i> Liste Görünümü
            </a>
            @can('schedules.create')
                <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yeni Ekle
                </a>
            @endcan
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- FullCalendar placeholder -->
            <div id="calendar">
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-slate-300 mb-3"></i>
                    <p class="text-slate-500">Takvim görünümü yakında drag-and-drop özelliğiyle burada olacak.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
