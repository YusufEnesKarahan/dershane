@extends('layouts.admin')

@section('title', 'Ödev İncele')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-slate-800">Ödev İncele: {{ $submission->student->user->name }}</h1>
        <a href="{{ route('admin.homeworks.submissions.index', $homework) }}" class="btn btn-secondary">
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Teslim Bilgileri</h6>
                </div>
                <div class="card-body">
                    <p><strong>Ödev:</strong> {{ $homework->title }}</p>
                    <p><strong>Öğrenci:</strong> {{ $submission->student->user->name }}</p>
                    <p><strong>Teslim Tarihi:</strong> {{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y H:i') : '-' }}</p>
                    <p>
                        <strong>Durum:</strong> 
                        @if($submission->status === 'submitted')
                            <span class="badge badge-success">Teslim Edildi</span>
                        @elseif($submission->status === 'late')
                            <span class="badge badge-warning">Geç Teslim</span>
                        @elseif($submission->status === 'graded')
                            <span class="badge badge-info">Notlandırıldı</span>
                        @else
                            <span class="badge badge-secondary">Bekliyor</span>
                        @endif
                    </p>

                    <h5 class="mt-4">Dosyalar</h5>
                    @if($submission->files->count() > 0)
                        <ul class="list-group">
                            @foreach($submission->files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $file->original_name }}
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        İndir
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Dosya yüklenmemiş.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notlandırma</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.homeworks.submissions.grade', [$homework, $submission]) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>Puan (Max: {{ $homework->max_score }})</label>
                            <input type="number" name="score" class="form-control" value="{{ old('score', $submission->score) }}" min="0" max="{{ $homework->max_score }}" required>
                        </div>

                        <div class="form-group">
                            <label>Geri Bildirim</label>
                            <textarea name="feedback" class="form-control" rows="4">{{ old('feedback', $submission->feedback) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Kaydet ve Bildir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
