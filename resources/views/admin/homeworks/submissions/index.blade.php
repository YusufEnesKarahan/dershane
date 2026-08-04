@extends('layouts.admin')

@section('title', 'Ödev Teslimleri')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Teslimler: {{ $homework->title }}</h1>
            <p class="text-muted mb-0">Son Teslim: {{ $homework->due_date->format('d.m.Y H:i') }} | Max Puan: {{ $homework->max_score }}</p>
        </div>
        <a href="{{ route('admin.homeworks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Öğrenci</th>
                            <th>Teslim Tarihi</th>
                            <th>Durum</th>
                            <th>Puan</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student->user->name }}</td>
                            <td>{{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                @if($submission->status === 'submitted')
                                    <span class="badge badge-success">Teslim Edildi</span>
                                @elseif($submission->status === 'late')
                                    <span class="badge badge-warning">Geç Teslim</span>
                                @elseif($submission->status === 'graded')
                                    <span class="badge badge-info">Notlandırıldı</span>
                                @else
                                    <span class="badge badge-secondary">Bekliyor</span>
                                @endif
                            </td>
                            <td>
                                @if($submission->score !== null)
                                    {{ $submission->score }} / {{ $homework->max_score }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.homeworks.submissions.show', [$homework, $submission]) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> İncele & Notlandır
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Henüz teslim bulunamadı.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
