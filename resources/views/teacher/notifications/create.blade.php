@extends('layouts.admin')

@section('title', 'Öğrenciye Bildirim Gönder')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Öğrenciye Bildirim Gönder</h2>
        <a href="{{ route('teacher.notifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('teacher.notifications.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="student_id">Öğrenci Seçiniz <span class="text-danger">*</span></label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="">Seçiniz...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="title">Bildirim Başlığı <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Örn: Ödev Hatırlatması" required>
                </div>

                <div class="form-group mb-3">
                    <label for="message">Mesaj Metni <span class="text-danger">*</span></label>
                    <textarea name="message" id="message" rows="4" class="form-control" placeholder="Bildirim içeriği..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gönder
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
