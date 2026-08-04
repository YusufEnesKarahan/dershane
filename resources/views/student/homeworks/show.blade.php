@extends('layouts.app')

@section('title', 'Ödev Detayı: ' . $homework->title)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Ödev: {{ $homework->title }}</h2>
        <a href="{{ route('student.homeworks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary">Ödev Açıklaması</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        <strong>Ders:</strong> {{ $homework->course->name }} <br>
                        <strong>Öğretmen:</strong> {{ $homework->teacher->user->name }}
                    </p>
                    
                    <div class="p-3 bg-light rounded border mb-4">
                        {!! nl2br(e($homework->description)) !!}
                    </div>

                    @if($homework->files && $homework->files->count() > 0)
                        <h6 class="mb-3">Ekler</h6>
                        <ul class="list-group">
                            @foreach($homework->files as $file)
                            <li class="list-group-item">
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank">
                                    <i class="fas fa-file-download me-2"></i> {{ $file->original_name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-top-primary">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary">Teslim Durumu</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <strong>Son Teslim:</strong> <br>
                            <span class="text-danger">{{ $homework->due_date->format('d.m.Y H:i') }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Durum:</strong> <br>
                            @if($submission)
                                @if($submission->status === 'submitted')
                                    <span class="badge bg-success">Teslim Edildi</span>
                                @elseif($submission->status === 'late')
                                    <span class="badge bg-warning text-dark">Geç Teslim</span>
                                @elseif($submission->status === 'graded')
                                    <span class="badge bg-info">Notlandırıldı</span>
                                @endif
                            @else
                                @if(now()->greaterThan($homework->due_date))
                                    <span class="badge bg-danger">Süresi Geçti</span>
                                @else
                                    <span class="badge bg-secondary">Bekliyor</span>
                                @endif
                            @endif
                        </li>
                        @if($submission && $submission->status === 'graded')
                        <li class="mb-2">
                            <strong>Puan:</strong> <br>
                            <span class="fs-5 text-primary fw-bold">{{ $submission->score }}</span> / {{ $homework->max_score }}
                        </li>
                        <li class="mb-2">
                            <strong>Öğretmen Notu:</strong> <br>
                            <div class="p-2 bg-light rounded">
                                {{ $submission->feedback ?: 'Geri bildirim yok.' }}
                            </div>
                        </li>
                        @endif
                    </ul>

                    <hr>

                    @if(!$submission || $submission->status !== 'graded')
                        @php
                            $isPastDue = now()->greaterThan($homework->due_date);
                            $canSubmit = !$isPastDue || $homework->allow_late_submission;
                        @endphp
                        
                        @if($canSubmit)
                            <form action="{{ route('student.homeworks.submit', $homework) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Dosya Yükle</label>
                                    <input type="file" name="files[]" class="form-control" multiple>
                                    <small class="text-muted">Ödevinizi dosya olarak yükleyebilirsiniz (Max 10MB/dosya).</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-paper-plane me-1"></i> 
                                    {{ $submission ? 'Teslimi Güncelle' : 'Ödevi Teslim Et' }}
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Bu ödevin son teslim süresi dolmuştur. Geç teslim kabul edilmemektedir.
                            </div>
                        @endif
                    @endif
                    
                    @if($submission && $submission->files->count() > 0)
                        <div class="mt-4">
                            <h6>Yüklediğiniz Dosyalar</h6>
                            <ul class="list-group list-group-flush">
                                @foreach($submission->files as $file)
                                <li class="list-group-item px-0">
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank">
                                        <i class="fas fa-file me-1"></i> {{ $file->original_name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
