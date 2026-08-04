@extends('layouts.admin')

@section('title', 'Ödev Düzenle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ödev Düzenle</h1>
        <a href="{{ route('admin.homeworks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.homeworks.update', $homework) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $homework->title) }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Açıklama</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $homework->description) }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Son Teslim Tarihi <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date', $homework->due_date->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Max Puan <span class="text-danger">*</span></label>
                        <input type="number" name="max_score" class="form-control" value="{{ old('max_score', $homework->max_score) }}" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Durum <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status', $homework->status) == 'draft' ? 'selected' : '' }}>Taslak</option>
                            <option value="published" {{ old('status', $homework->status) == 'published' ? 'selected' : '' }}>Yayında</option>
                            <option value="closed" {{ old('status', $homework->status) == 'closed' ? 'selected' : '' }}>Kapalı</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allowLate" name="allow_late_submission" {{ old('allow_late_submission', $homework->allow_late_submission) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allowLate">Geç Teslime İzin Ver</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
