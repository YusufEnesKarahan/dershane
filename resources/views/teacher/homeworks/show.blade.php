@extends('layouts.app')

@section('title', 'Ödev Detayı ve Teslimler')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $homework->title }}</h2>
        <a href="{{ route('teacher.homeworks.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Ders:</strong> {{ $homework->course->name }} | <strong>Sınıf:</strong> {{ $homework->classroom->name }}</p>
            <p><strong>Son Teslim:</strong> <span class="text-danger">{{ $homework->due_date->format('d.m.Y H:i') }}</span></p>
            <p><strong>Max Puan:</strong> {{ $homework->max_score }}</p>
            <hr>
            <p>{!! nl2br(e($homework->description)) !!}</p>
        </div>
    </div>

    <h4 class="mb-3">Öğrenci Teslimleri</h4>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Öğrenci</th>
                            <th>Teslim Durumu</th>
                            <th>Tarih</th>
                            <th>Puan</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->student->user->name }}</td>
                            <td>
                                @if($submission->status === 'submitted')
                                    <span class="badge bg-success">Zamanında</span>
                                @elseif($submission->status === 'late')
                                    <span class="badge bg-warning text-dark">Geç</span>
                                @elseif($submission->status === 'graded')
                                    <span class="badge bg-info">Notlandırıldı</span>
                                @else
                                    <span class="badge bg-secondary">Bekliyor</span>
                                @endif
                            </td>
                            <td>{{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                @if($submission->score !== null)
                                    <strong class="text-primary">{{ $submission->score }}</strong> / {{ $homework->max_score }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gradeModal{{ $submission->id }}">
                                    <i class="fas fa-edit"></i> Notlandır
                                </button>

                                <!-- Grade Modal -->
                                <div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('teacher.homeworks.submissions.grade', [$homework, $submission]) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Notlandır: {{ $submission->student->user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Dosyalar</label>
                                                        @if($submission->files->count() > 0)
                                                            <ul class="list-group mb-3">
                                                                @foreach($submission->files as $file)
                                                                    <li class="list-group-item">
                                                                        <a href="{{ \Illuminate\Support\Facades\Storage::disk($file->disk)->url($file->path) }}" target="_blank">
                                                                            <i class="fas fa-file-download me-1"></i> {{ $file->original_name }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-muted small">Dosya yok.</p>
                                                        @endif
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Puan (Max: {{ $homework->max_score }})</label>
                                                        <input type="number" name="score" class="form-control" value="{{ old('score', $submission->score) }}" min="0" max="{{ $homework->max_score }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Geri Bildirim (Opsiyonel)</label>
                                                        <textarea name="feedback" class="form-control" rows="3">{{ old('feedback', $submission->feedback) }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" class="btn btn-success">Kaydet</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Henüz teslim yok.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
