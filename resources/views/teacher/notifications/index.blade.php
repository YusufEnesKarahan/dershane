@extends('layouts.admin')

@section('title', 'Bildirimlerim & Mesaj Gönderimi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Bildirimlerim & Öğrenci İletişimi</h2>
        <a href="{{ route('teacher.notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Öğrenciye Bildirim Gönder
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gelen Bildirimler</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tür</th>
                            <th>Başlık</th>
                            <th>Mesaj</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notif)
                        <tr>
                            <td><span class="badge badge-info">{{ strtoupper($notif->type ?? 'SYSTEM') }}</span></td>
                            <td><strong>{{ $notif->title }}</strong></td>
                            <td>{{ $notif->message ?? $notif->content }}</td>
                            <td class="small">{{ $notif->created_at ? $notif->created_at->format('d.m.Y H:i') : '-' }}</td>
                            <td>
                                @if($notif->isRead())
                                    <span class="badge badge-success">Okundu</span>
                                @else
                                    <form action="{{ route('teacher.notifications.read', $notif) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-primary">Okundu Yap</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Bildiriminiz bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
