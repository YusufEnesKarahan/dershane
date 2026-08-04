@extends('layouts.app')
@section('content')
<div class='container'>
    <h1>Ödev Detayı: {{ $homework->title }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Ders: {{ $homework->course->name ?? '-' }}</h5>
            <p class="card-text">{{ $homework->description }}</p>
            <ul>
                <li>Öğretmen: {{ $homework->teacher->user->name ?? '-' }}</li>
                <li>Son Teslim: {{ $homework->due_date ? $homework->due_date->format('d.m.Y H:i') : 'Yok' }}</li>
                <li>Maksimum Puan: {{ $homework->max_score }}</li>
            </ul>
        </div>
    </div>

    <h3>Öğrenci Teslim Durumları</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Öğrenci</th>
                <th>Durum</th>
                <th>Puan</th>
                <th>Öğretmen Notu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($homework->submissions as $submission)
            <tr>
                <td>{{ $submission->student->user->name ?? '-' }}</td>
                <td>{{ $submission->status }}</td>
                <td>{{ $submission->grade ?? '-' }}</td>
                <td>{{ $submission->teacher_feedback ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
