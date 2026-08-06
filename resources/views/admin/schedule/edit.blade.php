@extends('layouts.admin')

@section('title', 'Ders Programı Düzenle')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-slate-800">Ders Programı Düzenle</h1>
        <a href="{{ route('admin.schedule.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.schedule.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="academic_term_id">Akademik Dönem <span class="text-danger">*</span></label>
                        <select name="academic_term_id" id="academic_term_id" class="form-control" required>
                            @foreach($academicTerms as $term)
                            <option value="{{ $term->id }}" {{ old('academic_term_id', $schedule->academic_term_id) == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="classroom_id">Sınıf <span class="text-danger">*</span></label>
                        <select name="classroom_id" id="classroom_id" class="form-control" required>
                            @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id', $schedule->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="course_id">Ders <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-control" required>
                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $schedule->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="teacher_id">Öğretmen <span class="text-danger">*</span></label>
                        <select name="teacher_id" id="teacher_id" class="form-control" required>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="day_of_week">Gün <span class="text-danger">*</span></label>
                        <select name="day_of_week" id="day_of_week" class="form-control" required>
                            @foreach(['Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba', 'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'] as $dayKey => $dayVal)
                            <option value="{{ $dayKey }}" {{ old('day_of_week', $schedule->day_of_week) == $dayKey ? 'selected' : '' }}>{{ $dayVal }} ({{ $dayKey }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="start_time">Başlangıç Saati <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', is_object($schedule->start_time) ? $schedule->start_time->format('H:i') : substr($schedule->start_time, 0, 5)) }}" required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="end_time">Bitiş Saati <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', is_object($schedule->end_time) ? $schedule->end_time->format('H:i') : substr($schedule->end_time, 0, 5)) }}" required>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="room">Derslik / Oda Adı</label>
                        <input type="text" name="room" id="room" class="form-control" value="{{ old('room', $schedule->room) }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Güncelle
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
