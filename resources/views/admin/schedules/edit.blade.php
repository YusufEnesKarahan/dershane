@extends('layouts.admin')

@section('title', 'Ders Programı Düzenle')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-slate-800">Ders Programı Düzenle</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academic_term_id">Eğitim Dönemi</label>
                        <select name="academic_term_id" id="academic_term_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\AcademicTerm::where('branch_id', auth()->user()->branch_id)->get() as $term)
                                <option value="{{ $term->id }}" {{ $schedule->academic_term_id == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="classroom_id">Sınıf</label>
                        <select name="classroom_id" id="classroom_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Classroom::where('branch_id', auth()->user()->branch_id)->get() as $classroom)
                                <option value="{{ $classroom->id }}" {{ $schedule->classroom_id == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_id">Ders</label>
                        <select name="course_id" id="course_id" class="form-control" required>
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Course::where('branch_id', auth()->user()->branch_id)->get() as $course)
                                <option value="{{ $course->id }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id">Öğretmen</label>
                        <select name="teacher_id" id="teacher_id" class="form-control">
                            <option value="">Seçiniz</option>
                            @foreach(\App\Models\Teacher::where('branch_id', auth()->user()->branch_id)->with('user')->get() as $teacher)
                                <option value="{{ $teacher->id }}" {{ $schedule->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="day_of_week">Gün</label>
                        <select name="day_of_week" id="day_of_week" class="form-control" required>
                            <option value="Monday" {{ $schedule->day_of_week == 'Monday' ? 'selected' : '' }}>Pazartesi</option>
                            <option value="Tuesday" {{ $schedule->day_of_week == 'Tuesday' ? 'selected' : '' }}>Salı</option>
                            <option value="Wednesday" {{ $schedule->day_of_week == 'Wednesday' ? 'selected' : '' }}>Çarşamba</option>
                            <option value="Thursday" {{ $schedule->day_of_week == 'Thursday' ? 'selected' : '' }}>Perşembe</option>
                            <option value="Friday" {{ $schedule->day_of_week == 'Friday' ? 'selected' : '' }}>Cuma</option>
                            <option value="Saturday" {{ $schedule->day_of_week == 'Saturday' ? 'selected' : '' }}>Cumartesi</option>
                            <option value="Sunday" {{ $schedule->day_of_week == 'Sunday' ? 'selected' : '' }}>Pazar</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_time">Başlangıç Saati</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{ $schedule->start_time->format('H:i') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_time">Bitiş Saati</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{ $schedule->end_time->format('H:i') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="room">Derslik / Salon</label>
                        <input type="text" name="room" id="room" class="form-control" value="{{ $schedule->room }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Güncelle</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">İptal</a>
            </form>
        </div>
    </div>
</div>
@endsection
