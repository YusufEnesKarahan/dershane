@extends('layouts.admin')

@section('title', 'Bildirimlerim')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Bildirimlerim</h2>
        <form action="{{ route('student.notifications.read-all') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-check-double"></i> Tümünü Okundu İşaretle
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gelen Kutusu</h6>
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
                                    <form action="{{ route('student.notifications.read', $notif) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline-primary">Okundu Yap</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Henüz bildiriminiz bulunmuyor.</td>
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
