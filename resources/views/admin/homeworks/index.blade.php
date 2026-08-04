@extends('layouts.admin')

@section('title', 'Ödev Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ödev Yönetimi</h1>
        @can('homework.create')
        <a href="{{ route('admin.homeworks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ödev Ekle
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Ders / Sınıf</th>
                            <th>Öğretmen</th>
                            <th>Son Teslim</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($homeworks as $homework)
                        <tr>
                            <td>{{ $homework->title }}</td>
                            <td>
                                <span class="badge badge-info">{{ $homework->course->name }}</span>
                                <span class="badge badge-secondary">{{ $homework->classroom->name }}</span>
                            </td>
                            <td>{{ $homework->teacher->user->name }}</td>
                            <td>{{ $homework->due_date->format('d.m.Y H:i') }}</td>
                            <td>
                                @if($homework->status === 'draft')
                                    <span class="badge badge-warning">Taslak</span>
                                @elseif($homework->status === 'published')
                                    <span class="badge badge-success">Yayında</span>
                                @else
                                    <span class="badge badge-dark">Kapalı</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.homeworks.submissions.index', $homework) }}" class="btn btn-sm btn-info" title="Teslimleri Gör">
                                    <i class="fas fa-tasks"></i>
                                </a>
                                @can('homework.update', $homework)
                                <a href="{{ route('admin.homeworks.edit', $homework) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @if($homework->status === 'draft')
                                    @can('homework.publish')
                                    <form action="{{ route('admin.homeworks.publish', $homework) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Yayınla">
                                            <i class="fas fa-bullhorn"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                                @can('homework.delete', $homework)
                                <form action="{{ route('admin.homeworks.destroy', $homework) }}" method="POST" class="d-inline" onsubmit="return confirm('Emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Kayıt bulunamadı.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
