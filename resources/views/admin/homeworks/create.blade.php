@extends('layouts.admin')

@section('title', 'Yeni Ödev Ekle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yeni Ödev Ekle</h1>
        <a href="{{ route('admin.homeworks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.homeworks.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Başlık <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Öğretmen <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Teacher::with('user')->get() as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Dönem <span class="text-danger">*</span></label>
                        <select name="academic_term_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\AcademicTerm::all() as $term)
                                <option value="{{ $term->id }}" {{ old('academic_term_id') == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Ders <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Course::all() as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Sınıf <span class="text-danger">*</span></label>
                        <select name="classroom_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Classroom::all() as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Açıklama</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Son Teslim Tarihi <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Max Puan <span class="text-danger">*</span></label>
                        <input type="number" name="max_score" class="form-control" value="{{ old('max_score', 100) }}" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Durum <span class="text-danger">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Yayınla</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allowLate" name="allow_late_submission" {{ old('allow_late_submission') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allowLate">Geç Teslime İzin Ver</label>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
