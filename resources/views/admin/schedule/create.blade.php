@extends('layouts.admin')

@section('title', 'Yeni Ders Programı Ekle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yeni Ders Programı Ekle</h1>
        <a href="{{ route('admin.schedule.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.schedule.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="academic_term_id">Akademik Dönem <span class="text-danger">*</span></label>
                        <select name="academic_term_id" id="academic_term_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach($academicTerms as $term)
                            <option value="{{ $term->id }}" {{ old('academic_term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="classroom_id">Sınıf <span class="text-danger">*</span></label>
                        <select name="classroom_id" id="classroom_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="course_id">Ders <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="teacher_id">Öğretmen <span class="text-danger">*</span></label>
                        <select name="teacher_id" id="teacher_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="day_of_week">Gün <span class="text-danger">*</span></label>
                        <select name="day_of_week" id="day_of_week" class="form-control" required>
                            <option value="Monday" {{ old('day_of_week') == 'Monday' ? 'selected' : '' }}>Pazartesi (Monday)</option>
                            <option value="Tuesday" {{ old('day_of_week') == 'Tuesday' ? 'selected' : '' }}>Salı (Tuesday)</option>
                            <option value="Wednesday" {{ old('day_of_week') == 'Wednesday' ? 'selected' : '' }}>Çarşamba (Wednesday)</option>
                            <option value="Thursday" {{ old('day_of_week') == 'Thursday' ? 'selected' : '' }}>Perşembe (Thursday)</option>
                            <option value="Friday" {{ old('day_of_week') == 'Friday' ? 'selected' : '' }}>Cuma (Friday)</option>
                            <option value="Saturday" {{ old('day_of_week') == 'Saturday' ? 'selected' : '' }}>Cumartesi (Saturday)</option>
                            <option value="Sunday" {{ old('day_of_week') == 'Sunday' ? 'selected' : '' }}>Pazar (Sunday)</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="start_time">Başlangıç Saati <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="end_time">Bitiş Saati <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', '09:40') }}" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="room">Derslik / Oda Adı</label>
                        <input type="text" name="room" id="room" class="form-control" placeholder="Örn: Derslik 101" value="{{ old('room') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Kaydet
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
