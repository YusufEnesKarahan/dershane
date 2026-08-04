@extends('layouts.admin')
@section('content')
<div class='container'>
    <h1>Öğrenci Ödevleri</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>Ders</th>
                <th>Başlık</th>
                <th>Son Teslim</th>
                <th>Öğretmen</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($homeworks as $homework)
            <tr>
                <td>{{ $homework->course->name }}</td>
                <td>{{ $homework->title }}</td>
                <td>{{ $homework->due_date ? $homework->due_date->format('d.m.Y H:i') : '-' }}</td>
                <td>{{ $homework->teacher->user->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('parent.homeworks.show', $homework->id) }}" class="btn btn-sm btn-primary">Görüntüle</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
