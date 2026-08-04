@extends('layouts.admin')

@section('title', 'Ödevlerim')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Ödevlerim</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($homeworks as $homework)
        @php
            $submission = $homework->submissions->first();
            $isPastDue = now()->greaterThan($homework->due_date);
            
            $cardClass = 'border-primary';
            $statusText = 'Bekliyor';
            $statusBadge = 'bg-secondary';
            
            if ($submission) {
                if ($submission->status === 'submitted') {
                    $cardClass = 'border-success';
                    $statusText = 'Teslim Edildi';
                    $statusBadge = 'bg-success';
                } elseif ($submission->status === 'late') {
                    $cardClass = 'border-warning';
                    $statusText = 'Geç Teslim';
                    $statusBadge = 'bg-warning text-dark';
                } elseif ($submission->status === 'graded') {
                    $cardClass = 'border-info';
                    $statusText = 'Notlandırıldı (' . $submission->score . '/' . $homework->max_score . ')';
                    $statusBadge = 'bg-info';
                }
            } elseif ($isPastDue) {
                $cardClass = 'border-danger';
                $statusText = 'Süresi Geçti';
                $statusBadge = 'bg-danger';
            }
        @endphp
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm h-100 {{ $cardClass }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title text-primary mb-0">{{ $homework->title }}</h5>
                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                    </div>
                    
                    <p class="text-muted small mb-3">
                        <i class="fas fa-book me-1"></i> {{ $homework->course->name }}<br>
                        <i class="fas fa-user-tie me-1"></i> {{ $homework->teacher->user->name }}
                    </p>
                    
                    <p class="mb-2 {{ $isPastDue && !$submission ? 'text-danger fw-bold' : '' }}">
                        <i class="far fa-clock me-1"></i> <strong>Son Teslim:</strong><br> 
                        {{ $homework->due_date->format('d.m.Y H:i') }}
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('student.homeworks.show', $homework) }}" class="btn btn-outline-primary btn-sm w-100">
                        Detay ve Teslim
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">Şu anda size atanmış aktif bir ödev bulunmuyor.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
