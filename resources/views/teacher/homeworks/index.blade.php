@extends('layouts.app')

@section('title', 'Ödev Yönetimi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Verdiğim Ödevler</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($homeworks as $homework)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title text-primary mb-0">{{ $homework->title }}</h5>
                        @if($homework->status === 'draft')
                            <span class="badge bg-warning text-dark">Taslak</span>
                        @elseif($homework->status === 'published')
                            <span class="badge bg-success">Yayında</span>
                        @else
                            <span class="badge bg-secondary">Kapalı</span>
                        @endif
                    </div>
                    
                    <p class="text-muted small mb-3">
                        {{ $homework->course->name }} - {{ $homework->classroom->name }}
                    </p>
                    
                    <p class="mb-2"><strong>Son Teslim:</strong> {{ $homework->due_date->format('d.m.Y H:i') }}</p>
                    <p class="mb-3">
                        <span class="text-muted">{{ Str::limit($homework->description, 100) }}</span>
                    </p>
                    
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('teacher.homeworks.show', $homework) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-eye"></i> İncele & Notlandır
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">Henüz verdiğiniz bir ödev bulunmuyor.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
